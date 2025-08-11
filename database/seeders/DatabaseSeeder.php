<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\EmasDemoSeeder;
use Database\Seeders\DemoMaugoSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed a demo user for login
        User::updateOrCreate(
            ['email' => 'demo@emas.test'],
            [
                'name' => 'Demo User',
                'username' => 'demo',
                'password' => Hash::make('Password123!'),
            ]
        );

        // Seed demo hierarchy, KWARAA school, subjects, students and marks
        $this->call([
            EmasDemoSeeder::class,
            DemoMaugoSeeder::class,
        ]);
    }
}
