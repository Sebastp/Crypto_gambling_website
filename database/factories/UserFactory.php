<?php

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

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'user_id' => App\User::uid(9),
        'nickname' => $faker->name,
        'url' => App\User::uStringId(60, 'url'), // secret
        'remember_token' => App\User::uStringId(10, 'remember_token')
    ];
});

$factory->define(App\Transaction::class, function (Faker $faker) {
    // $TransAmm = rand(0.0001, 100.0000);
    $TransAmm = 10.0000;
    return [
        'type' => 'deposit',
        'payment_id' => App\Transaction::uStringId(60, 'payment_id'),
        'address' => App\Transaction::uStringId(60, 'payment_id'),
        'amount' => $TransAmm
    ];
});
