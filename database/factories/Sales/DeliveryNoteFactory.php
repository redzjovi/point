<?php

use App\Model\Form;
use App\Model\Master\Customer;
use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\Model\Sales\DeliveryNote\DeliveryNoteItem;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(DeliveryNote::class, function (Faker $faker) {
    return [
        'customer_id' => factory(Customer::class),
        'customer_name' => function (array $deliveryNote) {
            /** @var Customer */
            $customer = Customer::query()->find($deliveryNote['customer_id']);

            return $customer->name;
        },
        'customer_phone' => function (array $deliveryNote) {
            /** @var Customer */
            $customer = Customer::query()->find($deliveryNote['customer_id']);

            return $customer->phone;
        },
        'customer_address' => function (array $deliveryNote) {
            /** @var Customer */
            $customer = Customer::query()->find($deliveryNote['customer_id']);

            return $customer->address;
        },
        'delivery_order_id' => factory(DeliveryOrder::class)->state('with_form'),
        'warehouse_id' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->warehouse_id;
        },
        'billing_address' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->billing_address;
        },
        'billing_phone' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->billing_phone;
        },
        'billing_address' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->billing_address;
        },
        'billing_email' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->billing_email;
        },
        'shipping_address' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->shipping_address;
        },
        'shipping_phone' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->shipping_phone;
        },
        'shipping_address' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->shipping_address;
        },
        'shipping_email' => function (array $deliveryNote) {
            /** @var DeliveryOrder */
            $deliveryOrder = DeliveryOrder::query()->find($deliveryNote['delivery_order_id']);

            return $deliveryOrder->shipping_email;
        },
        // 'warehouse_id' => factory(Warehouse::class),
        // 'delivery_order_id' => factory(DeliveryOrder::class),
        'driver' => $faker->name,
        'license_plate' => $faker->postcode,
    ];
});

$factory->afterCreatingState(DeliveryNote::class, 'with_form', function (DeliveryNote $deliveryNote) {
    $deliveryNote->form()->save(
        factory(Form::class)->make()
    );
});

$factory->afterCreatingState(DeliveryNote::class, 'with_items', function (DeliveryNote $deliveryNote) {
    $deliveryNote->items()->saveMany(
        factory(DeliveryNoteItem::class, 2)->make()
    );
});
