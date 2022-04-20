<?php

use App\Model\Form;
use App\Model\Master\Supplier;
use App\Model\Purchase\PurchaseInvoice\PurchaseInvoice;
use App\Model\Purchase\PurchaseInvoice\PurchaseInvoiceItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(PurchaseInvoice::class, function (Faker $faker) {
    return [
        'supplier_id' => factory(Supplier::class),
        'supplier_name' => function (array $purchaseInvoice) {
            /** @var Supplier */
            $supplier = Supplier::query()->find($purchaseInvoice['supplier_id']);

            return $supplier->name;
        },
        'due_date' => date('Y-m-d'),
        'delivery_fee' => $faker->randomNumber(),
        'discount_value' => $faker->randomNumber(),
        'type_of_tax' => $faker->randomKey([
            PurchaseInvoice::TYPE_OF_TAX_EXCLUDE,
            PurchaseInvoice::TYPE_OF_TAX_NON,
            PurchaseInvoice::TYPE_OF_TAX_INCLUDE,
        ]),
        'tax' => 0,
        'amount' => $faker->randomNumber(),
        'remaining' => $faker->randomNumber(),
    ];
});

$factory->afterCreatingState(PurchaseInvoice::class, 'with_form_approval', function (PurchaseInvoice $purchaseInvoice) {
    $purchaseInvoice->form()->save(
        factory(Form::class)->state('approval')->make()
    );
});

$factory->afterCreatingState(PurchaseInvoice::class, 'with_items', function (PurchaseInvoice $purchaseInvoice) {
    $purchaseInvoice->items()->saveMany(
        factory(PurchaseInvoiceItem::class, 2)->make()
    );
});
