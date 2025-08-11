<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssignMlimaniCodeSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('schools')) {
            $this->command?->warn('schools table not found');
            return;
        }

        // Try to find Mlimani Secondary by common name columns
        $query = DB::table('schools');
        $nameCol = null;
        foreach (['name','school_name','title'] as $col) {
            if (Schema::hasColumn('schools', $col)) { $nameCol = $col; break; }
        }
        if (!$nameCol) {
            $this->command?->warn('No name column found in schools table');
            return;
        }

        $school = DB::table('schools')->where($nameCol, 'like', '%Mlimani Secondary%')->first();
        if (!$school) {
            $this->command?->warn('Mlimani Secondary not found');
            return;
        }

        // Choose the first available code column in our priority list
        $codeCol = null;
        foreach (['code','school_code','emis_code','reg_no','registration_no'] as $col) {
            if (Schema::hasColumn('schools', $col)) { $codeCol = $col; break; }
        }
        if (!$codeCol) {
            $this->command?->warn('No code-like column found to set');
            return;
        }

        // Set a sample code; normalized without leading S.
        $sample = 'MLIMANI';
        DB::table('schools')->where('id', $school->id)->update([$codeCol => $sample]);
        $this->command?->info("Set {$codeCol}='{$sample}' for Mlimani Secondary (id={$school->id})");
    }
}
