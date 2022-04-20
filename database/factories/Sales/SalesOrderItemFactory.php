<?php

use App\Model\Master\Allocation;
use App\Model\Sales\SalesOrder\SalesOrder;
use App\Model\Sales\SalesOrder\SalesOrderItem;
use App\Model\Sales\SalesQuotation\SalesQuotationItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SalesOrderItem::class, function (Faker $faker) {
    return [
        'sales_order_id' => factory(SalesOrder::class),
        'sales_quotation_item_id' => factory(SalesQuotationItem::class),
        'item_id' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->item_id;
        },
        'item_name' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->item_name;
        },
        'quantity' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->quantity;
        },
        'price' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->price;
        },
        'discount_percent' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->discount_percent;
        },
        'discount_value' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->discount_value;
        },
        'taxable' => $faker->numberBetween(0, 1),
        'unit' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->unit;
        },
        'converter' => function (array $salesOrderItem) {
            /** @var SalesQuotationItem */
            $salesQuotationItem = SalesQuotationItem::query()->find($salesOrderItem['sales_quotation_item_id']);

            return $salesQuotationItem->converter;
        },
        'notes' => $faker->text(),
        'allocation_id' => factory(Allocation::class),
    ];
});
