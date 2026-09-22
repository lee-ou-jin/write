<?php

namespace Database\Factories;

use App\Models\ShipsCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ship>
 */
class ShipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ships_company_id' => ShipsCompany::factory(),
            'name' => $this->faker->colorName(),
            'imo_number' => $this->faker->randomNumber(),
            'type' => $this->faker->randomElement([0, 1, 2, 3]), // 0 기타, 1 화물선, 2 여객선, 3 유조선
            'status' => $this->faker->randomElement([0, 1, 2, 3]), // 0 기타, 1 운항중, 2 정박중, 3 수리중
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
