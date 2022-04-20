<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Purchase\PurchaseReceive\PurchaseReceive;
use App\Model\Purchase\PurchaseReceive\PurchaseReceiveItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(PurchaseReceiveItem::class, function (Faker $faker) {
    return [
        'purchase_receive_id' => factory(PurchaseReceive::class),
        'item_id' => factory(Item::class)->state('with_item_units'),
        'item_name' => function (array $purchaseReceiveItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseReceiveItem['item_id']);

            return $item->name;
        },
        'quantity' => $faker->numberBetween(10, 100),
        'price' => $faker->randomNumber(),
        'discount_value' => $faker->randomNumber(),
        'taxable' => $faker->numberBetween(0, 1),
        'unit' => function (array $purchaseReceiveItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseReceiveItem['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter' => function (array $purchaseReceiveItem) {
            /** @var Item */
            $item = Item::query()->find($purchaseReceiveItem['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
    ];
});
