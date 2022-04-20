<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use App\Model\Sales\DeliveryOrder\DeliveryOrderItem;
use App\Model\Sales\SalesOrder\SalesOrderItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(DeliveryOrderItem::class, function (Faker $faker) {
    return [
        'delivery_order_id' => factory(DeliveryOrder::class),
        'sales_order_item_id' => factory(SalesOrderItem::class),
        'item_id' => factory(Item::class)->state('with_item_units'),
        'item_name' => function (array $deliveryOrderItem) {
            /** @var Item */
            $item = Item::query()->find($deliveryOrderItem['item_id']);

            return $item->name;
        },
        'quantity' => $faker->numberBetween(1, 10),
        'price' => function (array $deliveryOrderItem) {
            /** @var SalesOrderItem */
            $salesOrderItem = SalesOrderItem::query()->find($deliveryOrderItem['sales_order_item_id']);

            return $salesOrderItem->price;
        },
        'discount_percent' => function (array $deliveryOrderItem) {
            /** @var SalesOrderItem */
            $salesOrderItem = SalesOrderItem::query()->find($deliveryOrderItem['sales_order_item_id']);

            return $salesOrderItem->discount_percent;
        },
        'discount_value' => function (array $deliveryOrderItem) {
            /** @var SalesOrderItem */
            $salesOrderItem = SalesOrderItem::query()->find($deliveryOrderItem['sales_order_item_id']);

            return $salesOrderItem->discount_value;
        },
        'taxable' => function (array $deliveryOrderItem) {
            /** @var SalesOrderItem */
            $salesOrderItem = SalesOrderItem::query()->find($deliveryOrderItem['sales_order_item_id']);

            return $salesOrderItem->taxable;
        },
        'unit' => function (array $deliveryOrderItem) {
            /** @var Item */
            $item = Item::query()->find($deliveryOrderItem['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter' => function (array $deliveryOrderItem) {
            /** @var Item */
            $item = Item::query()->find($deliveryOrderItem['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
        'notes' => $faker->text(),
        'allocation_id' => function (array $deliveryOrderItem) {
            /** @var SalesOrderItem */
            $salesOrderItem = SalesOrderItem::query()->find($deliveryOrderItem['sales_order_item_id']);

            return $salesOrderItem->allocation_id;
        },
    ];
});
