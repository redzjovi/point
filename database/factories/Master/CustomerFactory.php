<?php

use App\Model\Master\Branch;
use App\Model\Master\Customer;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Customer::class, function (Faker $faker) {
    return [
        'code' => uniqid(),
        'tax_identification_number' => uniqid(),
        'name' => $faker->name,
        'address' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'zip_code' => $faker->postcode,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'phone' => $faker->e164PhoneNumber,
        'phone_cc' => $faker->e164PhoneNumber,
        'email' => $faker->unique()->safeEmail,
        'notes' => $faker->text(),
        'credit_limit' => $faker->randomNumber(),
        'branch_id' => factory(Branch::class),
    ];
});
