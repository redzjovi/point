<?php

use App\Model\Master\Supplier;
use App\Model\Master\Warehouse;
use App\Model\Purchase\PurchaseReceive\PurchaseReceive;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(PurchaseReceive::class, function (Faker $faker) {
    return [
        'supplier_id' => factory(Supplier::class),
        'supplier_name' => function (array $purchaseReceive) {
            /** @var Supplier */
            $supplier = Supplier::query()->find($purchaseReceive['supplier_id']);

            return $supplier->name;
        },
        'warehouse_id' => factory(Warehouse::class),
        'warehouse_name' => function (array $purchaseReceive) {
            /** @var Warehouse */
            $warehouse = Warehouse::query()->find($purchaseReceive['warehouse_id']);

            return $warehouse->name;
        },
    ];
});
