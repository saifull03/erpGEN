<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(ProductSeeder::class);

        $adminRole = Role::query()->where('slug', 'super-admin')->first();

        User::query()->updateOrCreate(
            ['email' => 'admin@onestop.local'],
            [
                'name' => 'OneStop Admin',
                'role_id' => $adminRole?->id,
                'phone' => '+8801700000000',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );
    }
}
