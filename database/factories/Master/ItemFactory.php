<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Item::class, function (Faker $faker) {
    return [
        // 'chart_of_account_id' => null,
        // 'code' => $faker->name,
        'name' => $faker->name,
        // 'size' => null,
        'color' => $faker->colorName,
        // 'weight' => null,
        'notes' => $faker->text(),
        'taxable' => $faker->numberBetween(0, 1),
        'require_production_number' => 0,
        'require_expiry_date' => 0,
        'stock' => $faker->numberBetween(100000, 1000000),
        'stock_reminder' => $faker->randomNumber(),
    ];
});

$factory->afterCreatingState(Item::class, 'with_item_units', function (Item $item) {
    $item->units()->saveMany(
        factory(ItemUnit::class, 1)->make()
    );

    /** @var ItemUnit */
    $itemUnit = $item->units->first();

    $item->unit_default = $itemUnit->id;
    $item->unit_default_purchase = $itemUnit->id;
    $item->unit_default_sales = $itemUnit->id;
    $item->save();
});

