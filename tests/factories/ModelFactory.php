<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

$factory->define(\Mvdstam\Oauth2ServerLaravel\Entities\Client::class, function(\Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->uuid,
        'secret' => $faker->unique()->uuid,
        'name' => $faker->userName,
        'redirect_uri' => $faker->optional()->url
    ];
});

$factory->define(\Mvdstam\Oauth2ServerLaravel\Entities\User::class, function(\Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->uuid,
        'username' => $faker->unique()->userName,
        'password' => \Illuminate\Support\Facades\Hash::make($faker->unique()->password)
    ];
});
