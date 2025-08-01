<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserCategorySeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            CategoryPermissionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
