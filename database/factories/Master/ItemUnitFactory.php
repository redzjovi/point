<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use Faker\Generator as Faker;

/** @var Factory $factory */
$factory->define(ItemUnit::class, function (Faker $faker) {
    $units = ['pcs', 'box', 'kg'];

    /** @var string */
    $randomUnit = $faker->randomElement($units);

    return [
        'label' => $randomUnit,
        'name' => $randomUnit,
        'converter' => 1,
        'disabled' => 0,
        'item_id' => factory(Item::class),
    ];
});
