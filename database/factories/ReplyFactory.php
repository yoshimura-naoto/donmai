<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reply;
use Faker\Generator as Faker;

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'comment_id' => function() {
            return factory(App\Comment::class)->create()->id;
        },
        'body' => $faker->text(250),
    ];
});
