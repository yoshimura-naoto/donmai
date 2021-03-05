<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PostTag;
use Faker\Generator as Faker;

$factory->define(PostTag::class, function (Faker $faker) {
    return [
        'post_id' => function() {
            return factory(App\Post::class)->create()->id;
        },
        'tag_id' => function() {
            return factory(App\Tag::class)->create()->id;
        },
    ];
});
