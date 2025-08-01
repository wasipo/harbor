<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resources = ['users', 'posts', 'categories', 'roles'];
        $actions = ['read', 'create', 'update', 'delete'];
        
        $resource = $this->faker->randomElement($resources);
        $action = $this->faker->randomElement($actions);
        $key = "{$resource}.{$action}";

        return [
            'id' => (string) Str::ulid(),
            'key' => $key,
            'resource' => $resource,
            'action' => $action,
            'display_name' => ucfirst($resource) . ' ' . ucfirst($action),
            'description' => $this->faker->sentence(),
        ];
    }
}