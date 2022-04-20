<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Master\Branch;
use App\Model\Master\User;
use Faker\Generator as Faker;

$factory->define(Branch::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address' => $faker->address,
        'phone' => $faker->e164PhoneNumber,
        'created_by' => factory(User::class),
        'updated_by' => factory(User::class),
    ];
});
