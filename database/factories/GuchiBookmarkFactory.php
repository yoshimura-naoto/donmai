<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GuchiBookmark;
use Faker\Generator as Faker;

$factory->define(GuchiBookmark::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'guchi_room_id' => function() {
            return factory(App\GuchiRoom::class)->create()->id;
        }
    ];
});
