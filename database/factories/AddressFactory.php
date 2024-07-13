<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory()->create(),
            'street' => $this->faker->streetName,
            'number' => $this->faker->buildingNumber,
            'neighborhood' => $this->faker->city,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'cep' => $this->faker->numerify('#####-###'),
            'complement' => $this->faker->optional()->secondaryAddress,
            'reference' => $this->faker->optional()->sentence,
            'latitude' => $this->faker->latitude(-14.870, -21.600),
            'longitude' => $this->faker->longitude(-49.700, -45.600),
        ];
    }
}
