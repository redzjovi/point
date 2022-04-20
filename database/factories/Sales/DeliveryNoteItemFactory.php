<?php

use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\Model\Sales\DeliveryNote\DeliveryNoteItem;
use App\Model\Sales\DeliveryOrder\DeliveryOrderItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(DeliveryNoteItem::class, function (Faker $faker) {
    return [
        'delivery_note_id' => factory(DeliveryNote::class),
        'delivery_order_item_id' => factory(DeliveryOrderItem::class),
        'item_id' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->item_id;
        },
        'item_name' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->item_name;
        },
        'gross_weight' => $faker->randomNumber(),
        'tare_weight' => $faker->randomNumber(),
        'net_weight' => $faker->randomNumber(),
        'quantity' => $faker->numberBetween(1, 10),
        'expiry_date' => $faker->dateTimeThisYear('+1 month'),
        'production_number' => uniqid(),
        'price' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->price;
        },
        'discount_percent' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->discount_percent;
        },
        'discount_value' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->discount_value;
        },
        'taxable' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->taxable;
        },
        'unit' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->unit;
        },
        'converter' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->converter;
        },
        'notes' => $faker->text(),
        'allocation_id' => function (array $deliveryNoteItem) {
            /** @var DeliveryOrderItem */
            $deliveryOrderItem = DeliveryOrderItem::query()->find($deliveryNoteItem['delivery_order_item_id']);

            return $deliveryOrderItem->allocation_id;
        },
    ];
});
