<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Sales\SalesQuotation\SalesQuotation;
use App\Model\Sales\SalesQuotation\SalesQuotationItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SalesQuotationItem::class, function (Faker $faker) {
    return [
        'sales_quotation_id' => factory(SalesQuotation::class),
        'item_id' => factory(Item::class)->state('with_item_units'),
        'item_name' => function (array $salesQuotationTime) {
            /** @var Item */
            $item = Item::query()->find($salesQuotationTime['item_id']);

            return $item->name;
        },
        'quantity' => $faker->numberBetween(1, 10),
        'unit' => function (array $salesQuotationTime) {
            /** @var Item */
            $item = Item::query()->find($salesQuotationTime['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter' => function (array $salesQuotationTime) {
            /** @var Item */
            $item = Item::query()->find($salesQuotationTime['item_id']);

            /** @var null|ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
        'price' => $faker->randomNumber(),
        'discount_percent' => random_int(0, 100),
        'discount_value' => $faker->randomNumber(),
        'notes' => $faker->text(),
    ];
});
