<?php

use App\Model\Form;
use App\Model\Inventory\Inventory;
use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Master\Warehouse;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'form_id' => factory(Form::class),
        'warehouse_id' => factory(Warehouse::class),
        'item_id' => factory(Item::class)->state('with_item_units'),
        'quantity' => $faker->numberBetween(10, 100),
        'need_recalculate' => 0,
        'quantity_reference' => $faker->numberBetween(10, 100),
        'unit_reference' => function (array $inventory) {
            /** @var Item */
            $item = Item::query()->find($inventory['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter_reference' => function (array $inventory) {
            /** @var Item */
            $item = Item::query()->find($inventory['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
    ];
});
