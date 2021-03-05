<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Donmai;
use Faker\Generator as Faker;

$factory->define(Donmai::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'post_id' => function() {
            return factory(App\Post::class)->create()->id;
        },
    ];
});
