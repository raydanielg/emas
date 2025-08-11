<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected static function getApiKey(): ?string
    {
        // Prefer DB setting if available
        try {
            if (DB::getPdo()) {
                if (DB::getSchemaBuilder()->hasTable('ai_settings')) {
                    $row = DB::table('ai_settings')->where('provider','gemini')->where('enabled', true)->orderByDesc('id')->first();
                    if ($row && !empty($row->api_key)) return $row->api_key;
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback to env
        }
        return env('GEMINI_API_KEY');
    }

    public static function generateOfficialSelectionLetter(object $selection, $items)
    {
        $apiKey = self::getApiKey();
        if (!$apiKey) return null;

        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $endpoint = 'https://generative-language.googleapis.com/v1beta/models/'.urlencode($model).':generateContent?key='.urlencode($apiKey);

        $teacherLines = collect($items)->map(function($r){
            $name = $r->teacher_name ?? ('Teacher #'.$r->teacher_id);
            $role = $r->role ?? '';
            $as = $r->assigned_as ?? '';
            $status = $r->status ?? '';
            return "- Name: {$name}; Role: {$role}; Assigned As: {$as}; Status: {$status}";
        })->implode("\n");

        $prompt = "Write an official government-style appointment letter in English for an examination marking exercise in Tanzania (NECTA context). Keep it formal and respectful. Include: heading (country/ministry/NECTA), reference line with selection ID, date, body paragraphs stating purpose, selected teachers list as a neat table, closing instructions, and a signature placeholder. Keep it concise and neutral. Use HTML only (no scripts). Teachers list: \n{$teacherLines}\nNotes: ".($selection->notes ?? 'None')."";

        $payload = [
            'contents' => [
                [ 'parts' => [ ['text' => $prompt] ] ]
            ],
        ];

        try {
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $resp = curl_exec($ch);
            if ($resp === false) return null;
            $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
            if ($code < 200 || $code >= 300) return null;
            $data = json_decode($resp, true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) return null;
            return $text;
        } catch (\Throwable $e) {
            Log::warning('Gemini generation failed: '.$e->getMessage());
            return null;
        }
    }
}
