<?php

use App\Model\Master\Allocation;
use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Purchase\PurchaseInvoice\PurchaseInvoiceItem;
use App\Model\Purchase\PurchaseReceive\PurchaseReceive;
use App\Model\Purchase\PurchaseReceive\PurchaseReceiveItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(PurchaseInvoiceItem::class, function (Faker $faker) {
    return [
        'purchase_invoice_id' => factory(PurchaseInvoiceItem::class),
        'purchase_receive_id' => factory(PurchaseReceive::class),
        'purchase_receive_item_id' => factory(PurchaseReceiveItem::class),
        'item_id' => factory(Item::class),
        'item_name' => function (array $purchaseInvoiceItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseInvoiceItem['item_id']);

            return $item->name;
        },
        'quantity' => $faker->numberBetween(10, 100),
        'price' => $faker->randomNumber(),
        'discount_value' => $faker->randomNumber(),
        'taxable' => $faker->numberBetween(0, 1),
        'unit' => function (array $purchaseInvoiceItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseInvoiceItem['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter' => function (array $purchaseInvoiceItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseInvoiceItem['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
        'notes' => $faker->text(),
        'allocation_id' => factory(Allocation::class),
    ];
});
