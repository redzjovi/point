<?php

use App\Model\Master\Warehouse;
use App\Model\Sales\SalesContract\SalesContract;
use App\Model\Sales\SalesOrder\SalesOrder;
use App\Model\Sales\SalesQuotation\SalesQuotation;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SalesOrder::class, function (Faker $faker) {
    return [
        'sales_quotation_id' => factory(SalesQuotation::class),
        'sales_contract_id' => factory(SalesContract::class),
        'customer_id' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->customer_id;
        },
        'customer_name' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->customer_name;
        },
        'customer_address' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->customer_address;
        },
        'customer_phone' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->customer_phone;
        },
        'billing_address' => $faker->address,
        'billing_phone' => $faker->e164PhoneNumber,
        'billing_email' => $faker->email,
        'shipping_address' => $faker->address,
        'shipping_phone' => $faker->e164PhoneNumber,
        'shipping_email' => $faker->email,
        'warehouse_id' => factory(Warehouse::class),
        'eta' => $faker->date(),
        'cash_only' => $faker->boolean(),
        'need_down_payment' => $faker->randomNumber(),
        'delivery_fee' => $faker->randomNumber(),
        'discount_percent' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->discount_percent;
        },
        'discount_value' => function (array $salesOrder) {
            /** @var SalesQuotation */
            $salesQuotation = SalesQuotation::query()->find($salesOrder['sales_quotation_id']);

            return $salesQuotation->discount_value;
        },
        'type_of_tax' => function (array $salesOrder) {
            /** @var SalesContract */
            $salesContract = SalesContract::query()->find($salesOrder['sales_contract_id']);

            return $salesContract->type_of_tax;
        },
        'tax' => function (array $salesOrder) {
            /** @var SalesContract */
            $salesContract = SalesContract::query()->find($salesOrder['sales_contract_id']);

            return $salesContract->tax;
        },
        'amount' => function (array $salesOrder) {
            /** @var SalesContract */
            $salesContract = SalesContract::query()->find($salesOrder['sales_contract_id']);

            return $salesContract->amount;
        },
    ];
});
