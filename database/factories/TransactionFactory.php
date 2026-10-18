<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 紐付け先を指定しなければ、ユーザーも一緒に作られる
            'user_id' => User::factory(),
            'type' => '支出',
            'amount' => fake()->numberBetween(100, 100000),
            // マイグレーションの enum に無い値を入れると落ちるので、ここから選ぶ
            'category' => fake()->randomElement(['食費', '日用品', '交通費', '趣味', '光熱費', 'その他']),
            'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'memo' => fake()->realText(20),
        ];
    }

    /**
     * 収入の取引にする。
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => '収入',
        ]);
    }

    /**
     * 支出の取引にする。
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => '支出',
        ]);
    }
}
