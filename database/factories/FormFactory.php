<?php

use App\Model\Form;
use App\Model\Master\Branch;
use App\Model\Master\User;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Form::class, function (Faker $faker) {
    $formables = [
        DeliveryOrder::class,
    ];
    
    /** @var DeliveryOrder */
    $formableType = $faker->randomElement($formables);
    
    return [
        'branch_id' => factory(Branch::class),
        'date' => date('Y-m-d'),
        'number' => uniqid(),
        // 'edited_number' => uniqid(),
        // 'edited_notes' => $faker->text(),
        'notes' => $faker->text(),
        'created_by' => factory(User::class),
        'updated_by' => factory(User::class),
        'done' => 0,
        'increment' => date('dHis'),
        'increment_group' => date('Ym'),
        'formable_id' => factory($formableType),
        'formable_type' => $formableType::$morphName,
        // 'request_approval_to' => factory(User::class),
        'approval_status' => 0,
    ];
});

$factory->state(Form::class, 'approval', function (Faker $faker) {
    return [
        'approval_by' => factory(User::class),
        'approval_at' => date('Y-m-d H:i:s'),
        'approval_reason' => $faker->text(),
        'approval_status' => 0,
    ];
});

$factory->state(Form::class, 'approval_approved', function (Faker $faker) {
    return [
        'approval_by' => factory(User::class),
        'approval_at' => date('Y-m-d H:i:s'),
        'approval_reason' => $faker->text(),
        'approval_status' => 1,
    ];
});

$factory->state(Form::class, 'approval_rejected', function (Faker $faker) {
    return [
        'approval_by' => factory(User::class),
        'approval_at' => date('Y-m-d H:i:s'),
        'approval_reason' => $faker->text(),
        'approval_status' => -1,
    ];
});

$factory->state(Form::class, 'cancellation', function (Faker $faker) {
    return [
        'cancellation_approval_at' => date('Y-m-d H:i:s'),
        'cancellation_approval_by' => factory(User::class),
        'cancellation_approval_reason' => $faker->text(),
        'cancellation_status' => 0,
    ];
});

$factory->state(Form::class, 'cancellation_approved', function (Faker $faker) {
    return [
        'cancellation_approval_at' => date('Y-m-d H:i:s'),
        'cancellation_approval_by' => factory(User::class),
        'cancellation_approval_reason' => $faker->text(),
        'cancellation_status' => 1,
    ];
});

$factory->state(Form::class, 'cancellation_rejected', function (Faker $faker) {
    return [
        'cancellation_approval_at' => date('Y-m-d H:i:s'),
        'cancellation_approval_by' => factory(User::class),
        'cancellation_approval_reason' => $faker->text(),
        'cancellation_status' => -1,
    ];
});
