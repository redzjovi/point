<?php

use App\Model\Master\Customer;
use App\Model\Sales\SalesContract\SalesContract;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SalesContract::class, function (Faker $faker) {
    return [
        'customer_id' => factory(Customer::class),
        'customer_name' => function (array $salesContract) {
            /** @var Customer */
            $customer = Customer::query()->find($salesContract['customer_id']);

            return $customer->name;
        },
        'cash_only' => $faker->boolean,
        'need_down_payment' => $faker->randomNumber(),
        'discount_percent' => random_int(0, 100),
        'discount_value' => $faker->randomNumber(),
        'type_of_tax' => $faker->randomKey([
            SalesContract::TYPE_OF_TAX_EXCLUDE,
            SalesContract::TYPE_OF_TAX_NON,
            SalesContract::TYPE_OF_TAX_INCLUDE,
        ]),
        'tax' => $faker->randomNumber(),
        'amount' => $faker->randomNumber(),
    ];
});
