<?php

namespace Database\Factories;

use App\Models\Ship;
use App\Models\ShipsCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmartGlasses>
 */
class SmartGlassesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
//        $shipFactory = Ship::factory();
        return [
            'ship_id' => null,
            'ships_company_id' => null,
            'model' => $this->faker->colorName(),
            'serial_number' => $this->faker->unique()->randomNumber(9),
            'firmware_version' => $this->faker->randomElement([0, 1, 2, 3]),
            'os_version' => $this->faker->randomElement([0, 1, 2, 3]),
            'status' => $this->faker->randomElement(['inactive', 'active']), // inactive, active
        ];
    }
}
