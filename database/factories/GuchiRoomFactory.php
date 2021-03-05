<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GuchiRoom;
use Faker\Generator as Faker;

$factory->define(GuchiRoom::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'title' => $faker->text(50),
        'genre' => App\Post::$genres[rand(0, 10)]['route'],
    ];
});
