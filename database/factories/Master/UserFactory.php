<?php

use App\Model\Master\User;
use App\User as AppUser;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
/** @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'id' => factory(AppUser::class),
        'name' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->name;
        },
        'first_name' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->first_name;
        },
        'last_name' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->last_name;
        },
        'address' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->address;
        },
        'phone' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->phone;
        },
        'email' => function (array $user) {
            /** @var AppUser */
            $appUser = AppUser::query()->find($user['id']);

            return $appUser->email;
        },
    ];
});
