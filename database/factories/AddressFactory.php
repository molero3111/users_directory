<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'post_code' => $this->faker->postcode(),
            'street' => $this->faker->streetAddress(),
        ];
    }
}
