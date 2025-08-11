<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\EmasDemoSeeder;
use Database\Seeders\DemoMaugoSeeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed sample users for single login + role-based redirects
        User::updateOrCreate(
            ['email' => 'admin@emas.test'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('Password123!'),
            ]
        );
        User::updateOrCreate(
            ['email' => 'superadmin@emas.test'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'role' => 'superadmin',
                'password' => Hash::make('Password123!'),
            ]
        );
        User::updateOrCreate(
            ['email' => 'hm@emas.test'],
            [
                'name' => 'Headmaster Demo',
                'username' => 'headmaster',
                'role' => 'headmaster',
                'password' => Hash::make('Password123!'),
            ]
        );

        // Seed demo hierarchy, KWARAA school, subjects, students and marks
        // Seed demo data if schema supports it; do not fail user seeding
        try {
            if (Schema::hasTable('regions') && Schema::hasTable('districts') && Schema::hasTable('wards') && Schema::hasTable('schools')) {
                $this->call([
                    EmasDemoSeeder::class,
                ]);
            }
        } catch (\Throwable $e) {
            // ignore demo errors
        }
        try {
            $this->call([ DemoMaugoSeeder::class ]);
        } catch (\Throwable $e) {
            // ignore demo errors
        }
    }
}
