<?php

use App\Model\Master\Branch;
use App\Model\Master\Warehouse;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Warehouse::class, function (Faker $faker) {
    return [
        'code' => uniqid(),
        'name' => $faker->name,
        'address' => $faker->address,
        'phone' => $faker->e164PhoneNumber,
        'notes' => $faker->text(),
        'branch_id' => factory(Branch::class),
    ];
});
