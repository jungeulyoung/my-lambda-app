<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),          // 랜덤 문장 (제목용)
            'content' => fake()->paragraphs(3, true), // 랜덤 문단 3개 (내용용)
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'), // (옵션) 1년 전부터 현재 사이 랜덤 날짜
        ];
    }
}
