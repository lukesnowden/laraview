<?php

use Faker\Generator as Faker;

$factory->define( \Laraview\Row::class, function (Faker $faker) {
    return [
        'forename' => $faker->firstName,
        'surname' => $faker->lastName,
        'company' => $faker->company,
        'email_address' => $faker->safeEmail,
        'group' => array_random( [ 'Retail', 'Trade' ] ),
        'status' => array_random( [ 'Active', 'Hidden' ] ),
    ];
});
