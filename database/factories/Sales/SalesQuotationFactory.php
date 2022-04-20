<?php

use App\Model\Master\Customer;
use App\Model\Sales\SalesQuotation\SalesQuotation;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SalesQuotation::class, function (Faker $faker) {
    return [
        'customer_id' => factory(Customer::class),
        'customer_name' => function (array $salesQuotation) {
            /** @var Customer */
            $customer = Customer::query()->find($salesQuotation['customer_id']);

            return $customer->name;
        },
        'customer_address' => function (array $salesQuotation) {
            /** @var Customer */
            $customer = Customer::query()->find($salesQuotation['customer_id']);

            return $customer->address;
        },
        'customer_phone' => function (array $salesQuotation) {
            /** @var Customer */
            $customer = Customer::query()->find($salesQuotation['customer_id']);

            return $customer->phone;
        },
        'discount_percent' => random_int(0, 10),
        'discount_value' => $faker->randomNumber,
        'amount' => $faker->randomNumber(),
    ];
});
