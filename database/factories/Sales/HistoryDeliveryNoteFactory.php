<?php

use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\Model\Sales\HistoryDeliveryNote\HistoryDeliveryNote;
use App\Model\Sales\HistoryDeliveryNote\HistoryDeliveryNoteItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(HistoryDeliveryNote::class, function (Faker $faker) {
    $activityTypes = [
        HistoryDeliveryNote::ACTVITY_TYPE_APPROVE,
        HistoryDeliveryNote::ACTVITY_TYPE_CREATE,
        HistoryDeliveryNote::ACTVITY_TYPE_DELETE,
        HistoryDeliveryNote::ACTVITY_TYPE_REJECT,
        HistoryDeliveryNote::ACTVITY_TYPE_SEND_EMAIL,
        HistoryDeliveryNote::ACTVITY_TYPE_UPDATE,
    ];

    /** @var string */
    $activityType = $faker->randomElement($activityTypes);

    return [
        'delivery_note_id' => factory(DeliveryNote::class)->states([
            'with_form',
            'with_items',
        ]),
        'delivery_order_id' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->delivery_order_id;
        },
        'warehouse_id' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->warehouse_id;
        },
        'driver' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->driver;
        },
        'license_plate' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->license_plate;
        },
        'notes' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->notes;
        },
        'request_by' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->form->created_by;
        },
        'approval_by' => function (array $historyDeliveryNote) {
            /** @var DeliveryNote */
            $deliveryNote = DeliveryNote::query()->find($historyDeliveryNote['delivery_note_id']);

            return $deliveryNote->form->approval_by;
        },
        'activity_type' => $activityType,
    ];
});

$factory->afterCreatingState(HistoryDeliveryNote::class, 'with_items', function (HistoryDeliveryNote $historyDeliveryNote) {
    $historyDeliveryNote->items()->saveMany(
        factory(HistoryDeliveryNoteItem::class, 2)->make()
    );
});
