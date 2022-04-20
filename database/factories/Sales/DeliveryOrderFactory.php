<?php

use App\Model\Form;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use App\Model\Sales\DeliveryOrder\DeliveryOrderItem;
use App\Model\Sales\SalesOrder\SalesOrder;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(DeliveryOrder::class, function () {
    return [
        // 'warehouse_id' => factory(Warehouse::class),
        'sales_order_id' => factory(SalesOrder::class),
        'warehouse_id' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->warehouse_id;
        },
        'customer_id' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->customer_id;
        },
        'customer_name' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->customer_name;
        },
        'customer_address' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->customer_address;
        },
        'customer_phone' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->customer_phone;
        },
        'billing_address' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->billing_address;
        },
        'billing_phone' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->billing_phone;
        },
        'billing_email' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->billing_email;
        },
        'shipping_address' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->shipping_address;
        },
        'shipping_phone' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->shipping_phone;
        },
        'shipping_email' => function (array $deliveryOrder) {
            /** @var SalesOrder */
            $salesOrder = SalesOrder::query()->find($deliveryOrder['sales_order_id']);

            return $salesOrder->shipping_email;
        },
    ];
});

$factory->afterCreatingState(DeliveryOrder::class, 'with_form', function (DeliveryOrder $deliveryOrder) {
    $deliveryOrder->form()->save(
        factory(Form::class)->make()
    );
});

$factory->afterCreatingState(DeliveryOrder::class, 'with_form_approval', function (DeliveryOrder $deliveryOrder) {
    $deliveryOrder->form()->save(
        factory(Form::class)->state('approval')->make()
    );
});

$factory->afterCreatingState(DeliveryOrder::class, 'with_items', function (DeliveryOrder $deliveryOrder) {
    $deliveryOrder->items()->saveMany(
        factory(DeliveryOrderItem::class, 2)->make()
    );
});
