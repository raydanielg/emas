<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmasDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Ensure region/district/ward exist
            $regionId = DB::table('regions')->where('name', 'MANYARA')->value('id');
            if (!$regionId) {
                $regionId = DB::table('regions')->insertGetId([
                    'name' => 'MANYARA',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $districtId = DB::table('districts')->where('name', 'BABATI (M)')->value('id');
            if (!$districtId) {
                $districtId = DB::table('districts')->insertGetId([
                    'region_id' => $regionId,
                    'name' => 'BABATI (M)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $wardId = DB::table('wards')->where('name', 'KWARAA')->value('id');
            if (!$wardId) {
                $wardId = DB::table('wards')->insertGetId([
                    'district_id' => $districtId,
                    'name' => 'KWARAA',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // School: KWARAA SECONDARY SCHOOL
            $schoolId = DB::table('schools')->where('name', 'KWARAA SECONDARY SCHOOL')->value('id');
            if (!$schoolId) {
                $schoolId = DB::table('schools')->insertGetId([
                    'ward_id' => $wardId,
                    'code' => 'S3399',
                    'name' => 'KWARAA SECONDARY SCHOOL',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Ensure a demo user exists (if app has no users)
            $demo = DB::table('users')->where('email','demo@emas.test')->first();
            $userId = DB::table('users')->min('id');
            if (!$userId) {
                $userId = DB::table('users')->insertGetId([
                    'name' => 'Demo Admin',
                    'email' => 'demo@emas.test',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Ensure demo@emas.test is assigned too
            $demoId = $demo->id ?? DB::table('users')->where('email','demo@emas.test')->value('id');

            // Assign user to KWARAA
            $existingAssign = DB::table('user_school_assignments')
                ->where('user_id', $userId)
                ->where('school_id', $schoolId)
                ->exists();
            if (!$existingAssign) {
                DB::table('user_school_assignments')->insert([
                    'user_id' => $userId,
                    'school_id' => $schoolId,
                    'form' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($demoId && !DB::table('user_school_assignments')->where(['user_id'=>$demoId,'school_id'=>$schoolId])->exists()) {
                DB::table('user_school_assignments')->insert([
                    'user_id' => $demoId,
                    'school_id' => $schoolId,
                    'form' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Subjects
            $subjects = [
                ['code' => 'CIV', 'name' => 'Civics'],
                ['code' => 'HIST', 'name' => 'History'],
                ['code' => 'GEO', 'name' => 'Geography'],
                ['code' => 'ENG', 'name' => 'English'],
                ['code' => 'KIS', 'name' => 'Kiswahili'],
                ['code' => 'BIO', 'name' => 'Biology'],
                ['code' => 'CHEM', 'name' => 'Chemistry'],
                ['code' => 'PHY', 'name' => 'Physics'],
                ['code' => 'MATH', 'name' => 'Mathematics'],
            ];
            $subjectIds = [];
            foreach ($subjects as $sub) {
                $sid = DB::table('subjects')->where('code', $sub['code'])->value('id');
                if (!$sid) {
                    $sid = DB::table('subjects')->insertGetId([
                        'code' => $sub['code'],
                        'name' => $sub['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $subjectIds[$sub['code']] = $sid;
            }

            // Students (about 30)
            $firstNames = ['ADELINA','AISHA','AGNES','ALICE','GLORY','GRACE','JOHN','PETER','DAVID','MARY','FATMA','HALIMA','JOYCE','SARAH','RUTH'];
            $lastNames = ['NGAMA','MOHAMEDI','KANYINYI','NGIDA','KAPUMBE','KHUFO','IBRAHIMU','KISAMO','GURTI','GWANDU','SAIDI','ABDALLAH','MSALULI','RAMADHANI','JUMA'];
            $studentsToCreate = 30;
            $createdStudentIds = [];
            for ($i = 0; $i < $studentsToCreate; $i++) {
                $fn = $firstNames[$i % count($firstNames)];
                $ln = $lastNames[$i % count($lastNames)];
                $sex = $i % 2 === 0 ? 'F' : 'M';
                $form = ($i % 4) + 1; // 1..4
                $exam = '2018'.str_pad((string)($i+1000), 4, '0', STR_PAD_LEFT).'156';
                $sid = DB::table('students')->insertGetId([
                    'school_id' => $schoolId,
                    'first_name' => $fn,
                    'last_name' => $ln,
                    'sex' => $sex,
                    'form' => $form,
                    'exam_number' => $exam,
                    'admitted' => (bool)($i % 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdStudentIds[] = $sid;
            }

            // Marks for each student in each subject (random demo numbers)
            foreach ($createdStudentIds as $idx => $studId) {
                $form = DB::table('students')->where('id', $studId)->value('form');
                foreach ($subjectIds as $code => $sid) {
                    // 0-100 score with 1 decimal
                    $score = round(mt_rand(30, 95) + mt_rand(0, 9)/10, 1);
                    DB::table('marks')->insert([
                        'student_id' => $studId,
                        'school_id' => $schoolId,
                        'subject_id' => $sid,
                        'entered_by' => $userId,
                        'form' => $form,
                        'score' => $score,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Assign subjects (edit rights) to users at KWARAA
            $assignSubject = function(int $u) use ($schoolId, $subjectIds) {
                $codes = ['ENG','MATH']; // allow editing English and Mathematics
                foreach ($codes as $c) {
                    if (!isset($subjectIds[$c])) continue;
                    $exists = DB::table('user_subject_assignments')
                        ->where(['user_id'=>$u,'school_id'=>$schoolId,'subject_id'=>$subjectIds[$c]])
                        ->exists();
                    if (!$exists) {
                        DB::table('user_subject_assignments')->insert([
                            'user_id' => $u,
                            'school_id' => $schoolId,
                            'subject_id' => $subjectIds[$c],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            };
            if ($userId) $assignSubject($userId);
            if ($demoId) $assignSubject($demoId);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
