<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GuchiGood;
use Faker\Generator as Faker;

$factory->define(GuchiGood::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'guchi_id' => function() {
            return factory(App\Guchi::class)->create()->id;
        }
    ];
});
