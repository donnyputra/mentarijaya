<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Store;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'code' => $faker->uuid,
        'name' => $faker->name,
        'address' => $faker->address,
        'phone_no' => $faker->phoneNumber,
    ];
});
