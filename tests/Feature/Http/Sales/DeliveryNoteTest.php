<?php

namespace Tests\Feature\Http\Sales;

use App\Model\Form;
use App\Model\Inventory\Inventory;
use App\Model\Master\Branch;
use App\Model\Master\Item;
use App\Model\Master\User;
use App\Model\Master\Warehouse;
use App\Model\Purchase\PurchaseInvoice\PurchaseInvoice;
use App\Model\Purchase\PurchaseInvoice\PurchaseInvoiceItem;
use App\Model\Purchase\PurchaseReceive\PurchaseReceive;
use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\Model\Sales\DeliveryNote\DeliveryNoteItem;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use App\Model\Sales\DeliveryOrder\DeliveryOrderItem;
use App\Model\Sales\HistoryDeliveryNote\HistoryDeliveryNote;
use App\User as AppUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DeliveryNoteTest extends TestCase
{
    private $url = '/api/v1/sales/delivery-notes';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('tenant:seed:dummy', ['db_name' => env('DB_TENANT_DATABASE')]);
        // purchase - receive
        // purchase - invoice
        // purchase - receive - approved, inventory - increase inventory, accounting - create journal
        // sales - delivery order
        // sales - delivery note, inventory - decrease inventory, accounting - create journal
        // $this->signIn();
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function create_delivery_note_test()
    {
        /** @var AppUser */
        $this->user = factory(AppUser::class)->create();

        $this->actingAs($this->user, 'api');

        /** @var User */
        $user = factory(User::class)->create(['id' => $this->user->id]);

        /** @var Branch */
        $branch = factory(Branch::class)->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        /** @var Warehouse */
        $warehouse = factory(Warehouse::class)->create(['branch_id' => $branch->id]);

        /** @var User */
        $user->branch_id = $branch->id;
        $user->warehouse_id = $warehouse->id;
        $user->save();
        
        $user->branches()->attach($branch->id, ['is_default' => 1]);

        /** @var PurchaseReceive */
        $purchaseReceive = factory(PurchaseReceive::class)->create(['warehouse_id' => $warehouse->id]);

        /** @var PurchaseInvoice */
        $purchaseInvoice = factory(PurchaseInvoice::class)->state('with_form_approval')->create();

        /** @var Collection<Item> */
        $items = factory(Item::class, 1)->state('with_item_units')->create();

        foreach ($items as $item) {
            $purchaseInvoice->items()->save(
                factory(PurchaseInvoiceItem::class)->make([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'purchase_receive_id' => $purchaseReceive->id,
                    'item_id' => $item->id,
                ])
            );    
        }

        $purchaseInvoice->load(['items', 'items.item']);

        foreach ($purchaseInvoice->items as $purchaseInvoiceItem) {
            factory(Inventory::class)->create([
                'form_id' => $purchaseInvoice->form->id,
                'warehouse_id' => $purchaseInvoiceItem->purchaseReceive->warehouse->id,
                'item_id' => $purchaseInvoiceItem->item_id,
                'quantity' => $purchaseInvoiceItem->quantity,
                'quantity_reference' => $purchaseInvoiceItem->quantity,
                'unit_reference' => $purchaseInvoiceItem->unit,
                'converter_reference' => $purchaseInvoiceItem->converter,
            ]);
        }

        /** @var DeliveryOrder */
        $deliveryOrder = factory(DeliveryOrder::class)->state('with_form_approval')->create([
            'warehouse_id' => $purchaseReceive->warehouse->id,
        ]);

        foreach ($items as $item) {
            $deliveryOrder->items()->save(
                factory(DeliveryOrderItem::class)->make([
                    'delivery_order_id' => $deliveryOrder->id,
                    'item_id' => $item->id,
                ])
            );
        }

        $deliveryOrder->load('items');

        /** @var User */
        $user = factory(User::class)->create();

        $requestData = [
            'date' => date('Y-m-d H:i:s'),
            'delivery_order_id' => $deliveryOrder->id,
            'warehouse_id' => $deliveryOrder->warehouse_id,
            'driver' => $this->faker->name,
            'license_plate' => $this->faker->postcode,
            'items' => $deliveryOrder->items->map(function (DeliveryOrderItem $deliveryOrderItem) {
                return [
                    'delivery_order_item_id' => $deliveryOrderItem->id,
                    'quantity' => $deliveryOrderItem->quantity,

                    'unit' => $deliveryOrderItem->unit, // TBC
                    'converter' => $deliveryOrderItem->converter, // TBC
                ];
            }),
            'notes' => $this->faker->text(),
            'request_approval_to' => $user->id,

            'increment_group' => date('Ym'), // TBC
        ];

        $response = $this->json('POST', $this->url, $requestData, $this->headers);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'delivery_order_id' => $requestData['delivery_order_id'],
            'warehouse_id' => $requestData['warehouse_id'],
            'driver' => $requestData['driver'],
            'license_plate' => $requestData['license_plate'],
        ]);

        $response->assertJsonFragment([
            'date' => $requestData['date'],
            'notes' => $requestData['notes'],
            'request_approval_to' => $requestData['request_approval_to'],
        ]);

        foreach ($requestData['items'] as $item) {
            $response->assertJsonFragment([
                'delivery_order_item_id' => $item['delivery_order_item_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'converter' => $item['converter'],
            ]);
        }

        $responseData = $response->decodeResponseJson();

        $deliveryNote = new DeliveryNote();

        $this->assertDatabaseHas($deliveryNote->getTable(), [
            'warehouse_id' => $requestData['warehouse_id'],
            'delivery_order_id' => $requestData['delivery_order_id'],
            'driver' => $requestData['driver'],
            'license_plate' => $requestData['license_plate'],
        ], $deliveryNote->getConnectionName());

        $form = new Form();

        $this->assertDatabaseHas($form->getTable(), [
            'date' => $requestData['date'],
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
            'done' => 0,
            'increment' => $responseData['data']['form']['increment'],
            'increment_group' => $responseData['data']['form']['increment_group'],
            'formable_id' => $responseData['data']['id'],
            'formable_type' => $deliveryNote::$morphName,
            'approval_status' => 0,
        ], $form->getConnectionName());

        foreach ($requestData['items'] as $i => $item) {
            $deliveryNoteItem = new DeliveryNoteItem();

            $this->assertDatabaseHas($deliveryNoteItem->getTable(), [
                'delivery_note_id' => $responseData['data']['id'],
                'item_id' => $responseData['data']['items'][$i]['item_id'],
                'item_name' => $responseData['data']['items'][$i]['item_name'],
                'quantity' => $item['quantity'],
                'price' => $responseData['data']['items'][$i]['price'],
                'discount_value' => $responseData['data']['items'][$i]['discount_value'],
                'taxable' => $responseData['data']['items'][$i]['taxable'],
                'unit' => $item['unit'],
                'converter' => $item['converter'],
            ], $deliveryNoteItem->getConnectionName());

            $inventory = new Inventory();

            $this->assertDatabaseHas($inventory->getTable(), [
                'form_id' => $responseData['data']['form']['id'],
                'warehouse_id' => $requestData['warehouse_id'],
                'item_id' => $responseData['data']['items'][$i]['item_id'],
                'quantity' => strval(number_format(-1 * abs($item['quantity']), 30)),
                'need_recalculate' => 0,
                'quantity_reference' => strval(number_format(-1 * abs($item['quantity']), 30)),
                'unit_reference' => $item['unit'],
                'converter_reference' => strval(number_format($item['converter'], 30)),
            ], $inventory->getConnectionName());
        }

        foreach ($deliveryOrder->items as $i => $deliveryOrderItem) {
            $item = $deliveryOrderItem->item;

            $this->assertDatabaseHas($item->getTable(), [
                'id' => $deliveryOrderItem->item_id,
                'name' => $deliveryOrderItem->item_name,
                'taxable' => $deliveryOrderItem->item->taxable,
                'stock' => $deliveryOrderItem->item->stock,
            ], $item->getConnectionName());
        }
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_note_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('GET', $this->url.'/'.$deliveryNote->id, [], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $deliveryNote->id,
            'customer_id' => $deliveryNote->customer_id,
            'customer_name' => $deliveryNote->customer_name,
            'customer_address' => $deliveryNote->customer_address,
            'customer_phone' => $deliveryNote->customer_phone,
            'billing_address' => $deliveryNote->billing_address,
            'billing_phone' => $deliveryNote->billing_phone,
            'billing_email' => $deliveryNote->billing_email,
            'shipping_address' => $deliveryNote->shipping_address,
            'shipping_phone' => $deliveryNote->shipping_phone,
            'shipping_email' => $deliveryNote->shipping_email,
            'warehouse_id' => $deliveryNote->warehouse_id,
            'delivery_order_id' => $deliveryNote->delivery_order_id,
            'driver' => $deliveryNote->driver,
            'license_plate' => $deliveryNote->license_plate,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function send_delivery_note_email_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $requestData = [
            'id' => $deliveryNote->id,
            'message' => $this->faker->text(),
        ];

        $response = $this->json('POST', $this->url.'/send-email', $requestData, $this->headers);

        $response->assertStatus(204);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function approve_delivery_note_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('PUT', $this->url.'/'.$deliveryNote->id.'/approve', [], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'updated_by' => $this->user->id,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
            'approval_by' => $this->user->id,
            'approval_status' => 1,
        ]);

        $form = new Form();

        $this->assertDatabaseHas($form->getTable(), [
            'updated_by' => $this->user->id,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
            'approval_by' => $this->user->id,
            'approval_status' => 1,
        ], $form->getConnectionName());
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function reject_delivery_note_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $requestData = [
            'reason' => $this->faker->text(),
        ];

        $response = $this->json('PUT', $this->url.'/'.$deliveryNote->id.'/reject', $requestData, $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'updated_by' => $this->user->id,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
            'approval_by' => $this->user->id,
            'approval_reason' => $requestData['reason'],
            'approval_status' => -1,
        ]);

        $form = new Form();

        $this->assertDatabaseHas($form->getTable(), [
            'updated_by' => $this->user->id,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
            'approval_by' => $this->user->id,
            'approval_reason' => $requestData['reason'],
            'approval_status' => -1,
        ], $form->getConnectionName());
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_test()
    {
        /** @var Collection<DeliveryNote> */
        $deliveryNotes = factory(DeliveryNote::class, 1)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('GET', $this->url, [
            'join' => 'form',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'limit' => 10,
        ], $this->headers);

        $response->assertStatus(200);

        foreach ($deliveryNotes as $deliveryNote) {
            $response->assertJsonFragment([
                'id' => $deliveryNote->id,
                'customer_id' => $deliveryNote->customer_id,
                'customer_name' => $deliveryNote->customer_name,
                'customer_address' => $deliveryNote->customer_address,
                'customer_phone' => $deliveryNote->customer_phone,
                'billing_address' => $deliveryNote->billing_address,
                'billing_phone' => $deliveryNote->billing_phone,
                'billing_email' => $deliveryNote->billing_email,
                'shipping_address' => $deliveryNote->shipping_address,
                'shipping_phone' => $deliveryNote->shipping_phone,
                'shipping_email' => $deliveryNote->shipping_email,
                'warehouse_id' => $deliveryNote->warehouse_id,
                'delivery_order_id' => $deliveryNote->delivery_order_id,
                'driver' => $deliveryNote->driver,
                'license_plate' => $deliveryNote->license_plate,
            ]);
        }
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_item_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_equal' => [
                'sales_delivery_note_item.item_id' => $deliveryNote->items->first()->item_id,
            ],
            'limit' => 10,
            'includes' => 'items',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'delivery_note_id' => $deliveryNote->id,
            'item_id' => $deliveryNote->items->first()->item_id,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_warehouse_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_equal' => [
                'warehouse_id' => $deliveryNote->warehouse_id,
            ],
            'limit' => 10,
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $deliveryNote->id,
            'warehouse_id' => $deliveryNote->warehouse_id,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_approval_status_pending_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states(['with_form', 'with_items'])->create();

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => ';approvalPending',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'approval_status' => 0,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_approval_status_approved_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->state('approval_approved')->make()
        );

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => ';approvalApproved',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'approval_status' => 1,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_approval_status_rejected_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->state('approval_rejected')->make()
        );

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => ';approvalRejected',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'approval_status' => -1,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_form_status_pending_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->make()
        );

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => 'notArchived;pending',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'done' => 0,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_form_status_done_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->make([
                'formable_id' => $deliveryNote->id,
                'formable_type' => $deliveryNote::$morphName,
                'done' => 1,
            ])
        );

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => 'notArchived;done',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'done' => 1,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_notes_with_advance_filter_form_status_cancelled_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->make([
                'formable_id' => $deliveryNote->id,
                'formable_type' => $deliveryNote::$morphName,
                'cancellation_status' => 1,
            ])
        );

        $response = $this->json('GET', $this->url, [
            'join' => 'form,items',
            'fields' => 'sales_delivery_note.*',
            'sort_by' => '-form.created_at',
            'filter_form' => 'notArchived;cancellationApproved',
            'limit' => 10,
            'includes' => 'form',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $deliveryNote->id]);

        $response->assertJsonFragment([
            'cancellation_status' => 1,
            'formable_id' => $deliveryNote->id,
            'formable_type' => $deliveryNote::$morphName,
        ]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_note_histories_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->state('approval_approved')->make()
        );
        $deliveryNote->histories()->saveMany(
            factory(HistoryDeliveryNote::class, 2)->state('with_items')->make(['delivery_note_id' => $deliveryNote->id])
        );

        $response = $this->json('GET', $this->url.'/'.$deliveryNote->id.'/histories', [
            'sort_by' => '-created_at',
            'limit' => 10,
            'includes' => 'items',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['delivery_note_id' => $deliveryNote->id]);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function get_delivery_note_history_archive_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->state('with_items')->create();
        $deliveryNote->form()->save(
            factory(Form::class)->state('approval_approved')->make()
        );
        $deliveryNote->histories()->saveMany(
            factory(HistoryDeliveryNote::class, 1)->state('with_items')->make(['delivery_note_id' => $deliveryNote->id])
        );
        
        /** @var HistoryDeliveryNote */
        $historyDeliveryNote = $deliveryNote->histories->first();

        $response = $this->json('GET', $this->url.'/'.$historyDeliveryNote->delivery_note_id.'/histories/'.$historyDeliveryNote->id, [], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment($historyDeliveryNote->toArray());
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function export_delivery_notes_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $response = $this->json('POST', $this->url.'/export', [
            'id' => $deliveryNote->id,
        ], $this->headers);

        $response->assertStatus(200);
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function update_delivery_note_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $requestData = [
            'items' => $deliveryNote->items->map(function (DeliveryNoteItem $deliveryNoteItem) {
                return [
                    'id' => $deliveryNoteItem->id,
                    'quantity' => $this->faker->numberBetween(0, 10),
                    'old_quantity' => $deliveryNoteItem->quantity,
                ];
            }),
            'notes' => $this->faker->text(),
        ];
    
        $response = $this->json('PUT', $this->url.'/'.$deliveryNote->id, $requestData, $this->headers);

        $response->assertStatus(200);

        $response->assertJsonFragment(['notes' => $requestData['notes']]);

        foreach ($requestData['items'] as $item) {
            $response->assertJsonFragment([
                'id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
            ]);
        }

        $responseData = $response->decodeResponseJson();

        $form = $deliveryNote->form;

        $this->assertDatabaseHas($form->getTable(), [
            'id' => $form->id,
            'notes' => $requestData['notes'],
        ], $form->getConnectionName());

        foreach ($requestData['items'] as $i => $item) {
            $deliveryNoteItem = new DeliveryNoteItem();

            $this->assertDatabaseHas($deliveryNoteItem->getTable(), [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
            ], $deliveryNoteItem->getConnectionName());

            $inventory = new Inventory();

            $this->assertDatabaseHas($inventory->getTable(), [
                'form_id' => $form->id,
                'item_id' => $responseData['data']['items'][$i]['item_id'],
                'quantity' => strval(number_format(-1 * abs($item['quantity']), 30)),
                'quantity_reference' => strval(number_format(-1 * abs($item['quantity']), 30)),
            ], $inventory->getConnectionName());

            $this->assertDatabaseHas($inventory->getTable(), [
                'form_id' => $form->id,
                'item_id' => $responseData['data']['items'][$i]['item_id'],
                'quantity' => strval(number_format(1 * abs($item['old_quantity']), 30)),
                'quantity_reference' => strval(number_format(-1 * abs($item['old_quantity']), 30)),
            ], $inventory->getConnectionName());
        }

        foreach ($deliveryNote->items as $i => $deliveryNoteItem) {
            $item = $deliveryNoteItem->item;

            $this->assertDatabaseHas($item->getTable(), [
                'id' => $deliveryNoteItem->item_id,
                'stock' => $deliveryNoteItem->item->stock,
            ], $item->getConnectionName());
        }
    }

    /**
     * @test
     * @group sales/delivery-notes
     */
    public function delete_delivery_note_test()
    {
        /** @var DeliveryNote */
        $deliveryNote = factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ])->create();

        $data = [
            'reason' => $this->faker->text(),
        ];
    
        $response = $this->json('DELETE', $this->url.'/'.$deliveryNote->id, $data, $this->headers);

        $response->assertStatus(204);
    }
}
