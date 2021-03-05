<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GuchiImage;
use Faker\Generator as Faker;

$factory->define(GuchiImage::class, function (Faker $faker) {
    return [
        'guchi_id' => function() {
            return factory(App\Guchi::class)->create()->id;
        },
        'path' => $faker->text(30),
    ];
});
