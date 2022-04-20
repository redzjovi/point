<?php

use App\Model\Master\Item;
use App\Model\Master\ItemUnit;
use App\Model\Sales\DeliveryNote\HistoryDeliveryNote;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(HistoryDeliveryNoteItem::class, function (Faker $faker) {
    return [
        'history_delivery_note_id' => factory(HistoryDeliveryNote::class),
        'item_id' => factory(Item::class)->state('with_item_units'),
        'item_name' => function (array $historyDeliveryNoteItem) {
            /** @var Item */
            $item = Item::query()->find($historyDeliveryNoteItem['item_id']);

            return $item->name;
        },
        'quantity_remaining' => $faker->numberBetween(10, 100),
        'quantity' => $faker->numberBetween(1, 10),
        'unit' => function (array $historyDeliveryNoteItem) {
            /** @var Item */
            $item = Item::query()->find($historyDeliveryNoteItem['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->name;
        },
        'converter' => function (array $historyDeliveryNoteItem) {
            /** @var Item */
            $item = Item::query()->find($historyDeliveryNoteItem['item_id']);
            
            /** @var ItemUnit */
            $itemUnit = $item->units->first();

            return $itemUnit->converter;
        },
    ];
});
