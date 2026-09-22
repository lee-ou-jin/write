<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'equipment_id' => Equipment::factory(),
            'type' => rand(0, 2), // 0: 기타, 1: 반복 작업, 2: 단일 작업
            'mode' => rand(0, 1), // 0: 실시간, 1: 오프라인
            'status' => rand(0, 1), // 0: 진행중, 1: 완료
            'created_at' => $this->faker->dateTimeBetween('-1 years'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years'),
        ];
    }
}
