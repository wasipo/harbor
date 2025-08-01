<?php

namespace Database\Factories;

use App\Models\UserCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserCategory>
 */
class UserCategoryFactory extends Factory
{
    protected $model = UserCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = $this->faker->unique()->randomElement(['engineer', 'sales', 'admin', 'hr', 'finance']);
        
        $names = [
            'engineer' => 'エンジニア',
            'sales' => '営業',
            'admin' => '管理部',
            'hr' => '人事部',
            'finance' => '経理部',
        ];

        return [
            'id' => (string) Str::ulid(),
            'code' => $code,
            'name' => $names[$code],
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}