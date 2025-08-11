<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Upsert Gemini key; admin can change later via UI or DB
        DB::table('ai_settings')->updateOrInsert(
            ['provider' => 'gemini'],
            [
                'api_key' => env('GEMINI_API_KEY', null),
                'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
                'enabled' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
