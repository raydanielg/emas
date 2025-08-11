<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DemoMaugoSeeder extends Seeder
{
    public function run(): void
    {
        $school = [
            'name' => 'Amss Secondary School',
            'school_name' => 'Amss Secondary School',
            'code' => 'AMSS',
            'school_code' => 'AMSS',
            'region' => 'Mwanza',
            'district' => 'Ilemela',
            'ward' => 'Kiseke B',
            'postal_address' => 'P.O. Box 735',
            'level' => 'Secondary',
            'website' => 'https://www.amss.ac.tz',
            'established' => 2019,
        ];

        // Resolve region/district/ward IDs if those tables exist
        $regionId = null; $districtId = null; $wardId = null;
        // Region
        if (Schema::hasTable('regions')) {
            $reg = DB::table('regions')->when(Schema::hasColumn('regions','name'), fn($q)=>$q->where('name','Mwanza'))
                ->when(!Schema::hasColumn('regions','name') && Schema::hasColumn('regions','region_name'), fn($q)=>$q->where('region_name','Mwanza'))
                ->first();
            if (!$reg) {
                $payload = [];
                if (Schema::hasColumn('regions','name')) $payload['name'] = 'Mwanza';
                if (Schema::hasColumn('regions','region_name')) $payload['region_name'] = 'Mwanza';
                if (!empty($payload)) {
                    $regionId = DB::table('regions')->insertGetId($payload);
                }
            } else { $regionId = $reg->id ?? null; }
        }
        // District
        if (Schema::hasTable('districts')) {
            $dist = DB::table('districts')->when(Schema::hasColumn('districts','name'), fn($q)=>$q->where('name','Ilemela'))
                ->when(!Schema::hasColumn('districts','name') && Schema::hasColumn('districts','district_name'), fn($q)=>$q->where('district_name','Ilemela'));
            if ($regionId && Schema::hasColumn('districts','region_id')) { $dist->where('region_id',$regionId); }
            $dist = $dist->first();
            if (!$dist) {
                $payload = [];
                if (Schema::hasColumn('districts','name')) $payload['name'] = 'Ilemela';
                if (Schema::hasColumn('districts','district_name')) $payload['district_name'] = 'Ilemela';
                if ($regionId && Schema::hasColumn('districts','region_id')) $payload['region_id'] = $regionId;
                if (!empty($payload)) { $districtId = DB::table('districts')->insertGetId($payload); }
            } else { $districtId = $dist->id ?? null; }
        }
        // Ward
        if (Schema::hasTable('wards')) {
            $w = DB::table('wards')->when(Schema::hasColumn('wards','name'), fn($q)=>$q->where('name','Kiseke B'))
                ->when(!Schema::hasColumn('wards','name') && Schema::hasColumn('wards','ward_name'), fn($q)=>$q->where('ward_name','Kiseke B'));
            if ($districtId && Schema::hasColumn('wards','district_id')) { $w->where('district_id',$districtId); }
            $w = $w->first();
            if (!$w) {
                $payload = [];
                if (Schema::hasColumn('wards','name')) $payload['name'] = 'Kiseke B';
                if (Schema::hasColumn('wards','ward_name')) $payload['ward_name'] = 'Kiseke B';
                if ($districtId && Schema::hasColumn('wards','district_id')) $payload['district_id'] = $districtId;
                if (!empty($payload)) { $wardId = DB::table('wards')->insertGetId($payload); }
            } else { $wardId = $w->id ?? null; }
        }

        // 1) Create or update school/centre
        $schoolId = null;
        $schoolCode = 'AMSS';
        $schoolName = $school['school_name'];
        if (Schema::hasTable('schools')) {
            $exists = DB::table('schools')->when(Schema::hasColumn('schools','code'), fn($q)=>$q->where('code',$schoolCode))
                ->when(!Schema::hasColumn('schools','code') && Schema::hasColumn('schools','school_code'), fn($q)=>$q->where('school_code',$schoolCode))
                ->first();
            if ($exists) {
                $schoolId = $exists->id ?? null;
                $update = [];
                foreach ([
                    'name' => $school['name'],
                    'school_name' => $school['school_name'],
                    'code' => $school['code'],
                    'school_code' => $school['school_code'],
                    'region' => $school['region'],
                    'district' => $school['district'],
                    'ward' => $school['ward'],
                    'postal_address' => $school['postal_address'],
                    'level' => $school['level'],
                    'website' => $school['website'],
                    'established' => $school['established'],
                ] as $k=>$v) {
                    if (!is_null($v) && Schema::hasColumn('schools',$k)) $update[$k] = $v;
                }
                if ($regionId && Schema::hasColumn('schools','region_id')) $update['region_id'] = $regionId;
                if ($districtId && Schema::hasColumn('schools','district_id')) $update['district_id'] = $districtId;
                if ($wardId && Schema::hasColumn('schools','ward_id')) $update['ward_id'] = $wardId;
                if (!empty($update)) DB::table('schools')->where('id',$schoolId)->update($update);
            } else {
                $data = [];
                foreach ($school as $k=>$v) { if (Schema::hasColumn('schools',$k)) $data[$k] = $v; }
                if (!isset($data['name']) && Schema::hasColumn('schools','name')) $data['name'] = $schoolName;
                if (!isset($data['code']) && Schema::hasColumn('schools','code')) $data['code'] = $schoolCode;
                if (!isset($data['school_code']) && Schema::hasColumn('schools','school_code')) $data['school_code'] = $schoolCode;
                if ($regionId && Schema::hasColumn('schools','region_id')) $data['region_id'] = $regionId;
                if ($districtId && Schema::hasColumn('schools','district_id')) $data['district_id'] = $districtId;
                if ($wardId && Schema::hasColumn('schools','ward_id')) $data['ward_id'] = $wardId;
                $schoolId = DB::table('schools')->insertGetId($data);
            }
        } elseif (Schema::hasTable('centres')) {
            $exists = DB::table('centres')->when(Schema::hasColumn('centres','code'), fn($q)=>$q->where('code',$schoolCode))
                ->when(!Schema::hasColumn('centres','code') && Schema::hasColumn('centres','reg_no'), fn($q)=>$q->where('reg_no','S.'.$schoolCode))
                ->first();
            if ($exists) {
                $schoolId = $exists->id ?? null;
                DB::table('centres')->where('id',$schoolId)->update(array_filter([
                    'name' => $school['name'],
                    'centre_name' => $school['school_name'],
                    'code' => $school['code'],
                    'reg_no' => 'S.'.$schoolCode,
                    'region' => $school['region'],
                    'district' => $school['district'],
                    'ward' => $school['ward'],
                    'postal_address' => $school['postal_address'],
                    'level' => $school['level'],
                    'website' => $school['website'],
                    'established' => $school['established'],
                ], fn($v)=>!is_null($v)));
            } else {
                $data = [];
                foreach (['name'=>'Amss Secondary School','centre_name'=>'Amss Secondary School','code'=>$schoolCode,'reg_no'=>'S.'.$schoolCode,'region'=>$school['region'],'district'=>$school['district'],'ward'=>$school['ward'],'postal_address'=>$school['postal_address'],'level'=>$school['level'],'website'=>$school['website'],'established'=>$school['established']] as $k=>$v) {
                    if (Schema::hasColumn('centres',$k)) $data[$k] = $v;
                }
                $schoolId = DB::table('centres')->insertGetId($data);
            }
        }

        // 2) Create headmaster user Maugo Joas
        $email = 'maugo.joas@demo.test';
        $userData = [];
        // Always set email if exists
        if (Schema::hasColumn('users','email')) { $userData['email'] = $email; }
        // Name fields
        if (Schema::hasColumn('users','name')) { $userData['name'] = 'Maugo Joas'; }
        if (Schema::hasColumn('users','first_name')) { $userData['first_name'] = 'Maugo'; }
        if (Schema::hasColumn('users','last_name')) { $userData['last_name'] = 'Joas'; }
        if (Schema::hasColumn('users','username')) { $userData['username'] = 'maugo.joas'; }
        // Role if present
        if (Schema::hasColumn('users','role')) { $userData['role'] = 'headmaster'; }
        // Password
        if (Schema::hasColumn('users','password')) { $userData['password'] = Hash::make('Password123!'); }
        // Create or update by email
        if (!empty($userData)) {
            DB::table('users')->updateOrInsert(['email'=>$email], $userData);
        }
        // Fetch user id (updateOrInsert doesn't return id)
        $user = DB::table('users')->where('email',$email)->first();
        $userId = $user->id ?? null;

        // 3) Assign school to user via available structures
        if ($userId) {
            // Preferred pivot: user_school_assignments
            if ($schoolId && Schema::hasTable('user_school_assignments')) {
                $pivot = ['user_id' => $userId, 'school_id' => $schoolId, 'form' => null];
                // unique on user_id,school_id,form
                $exists = DB::table('user_school_assignments')->where($pivot)->first();
                if (!$exists) DB::table('user_school_assignments')->insert($pivot);
            // Common alternative pivots
            } elseif (Schema::hasTable('school_user')) {
                $pivot = ['user_id'=>$userId];
                if ($schoolId) $pivot['school_id'] = $schoolId;
                DB::table('school_user')->updateOrInsert($pivot, $pivot);
            } elseif (Schema::hasTable('user_school')) {
                $pivot = ['user_id'=>$userId];
                if ($schoolId) $pivot['school_id'] = $schoolId;
                DB::table('user_school')->updateOrInsert($pivot, $pivot);
            }
            // Fallback mapping for headmaster_students (if used in this app)
            if (Schema::hasTable('headmaster_students')) {
                $exists = DB::table('headmaster_students')
                    ->where('user_id',$userId)
                    ->when(Schema::hasColumn('headmaster_students','school_code'), fn($q)=>$q->where('school_code',$schoolCode))
                    ->first();
                if (!$exists) {
                    $data = ['user_id'=>$userId];
                    if (Schema::hasColumn('headmaster_students','school_code')) $data['school_code'] = $schoolCode;
                    if (Schema::hasColumn('headmaster_students','school_name')) $data['school_name'] = $schoolName;
                    DB::table('headmaster_students')->insert($data);
                }
            }
        }

        // 4) Create demo students for the school
        $students = [
            ['first_name'=>'John','last_name'=>'Moses','gender'=>'M','class'=>'Form II'],
            ['first_name'=>'Asha','last_name'=>'Peter','gender'=>'F','class'=>'Form II'],
            ['first_name'=>'Neema','last_name'=>'Joseph','gender'=>'F','class'=>'Form IV'],
            ['first_name'=>'Abel','last_name'=>'Luka','gender'=>'M','class'=>'Form IV'],
            ['first_name'=>'Grace','last_name'=>'Paul','gender'=>'F','class'=>'Form IV'],
        ];

        $admissionIndex = 1;
        foreach ($students as $s) {
            $adm = sprintf('S.%s.%04d', $schoolCode, $admissionIndex++);
            if (Schema::hasTable('students')) {
                $data = [];
                // required columns in our schema: school_id, first_name, last_name
                if ($schoolId && Schema::hasColumn('students','school_id')) $data['school_id'] = $schoolId;
                if (Schema::hasColumn('students','first_name')) $data['first_name'] = $s['first_name'];
                if (Schema::hasColumn('students','last_name')) $data['last_name'] = $s['last_name'];
                // optional columns in our schema
                if (Schema::hasColumn('students','sex')) $data['sex'] = $s['gender'] === 'F' ? 'F' : 'M';
                if (Schema::hasColumn('students','form')) {
                    $data['form'] = null;
                    if (str_contains($s['class'], 'II')) $data['form'] = 2;
                    elseif (str_contains($s['class'], 'IV')) $data['form'] = 4;
                }
                if (Schema::hasColumn('students','exam_number')) $data['exam_number'] = $adm;
                if (!empty($data)) {
                    DB::table('students')->insert($data);
                }
            } elseif (Schema::hasTable('headmaster_students')) {
                $data = ['user_id'=>$userId];
                foreach ([
                    'admission_number' => $adm,
                    'first_name' => $s['first_name'],
                    'last_name' => $s['last_name'],
                    'gender' => $s['gender'],
                    'class' => $s['class'],
                    'school_code' => $schoolCode,
                    'school_name' => $schoolName,
                ] as $k=>$v) { if (Schema::hasColumn('headmaster_students',$k)) $data[$k] = $v; }
                DB::table('headmaster_students')->insert($data);
            }
        }

        // 5) Optional: create a sample subject row if table exists
        if (Schema::hasTable('subjects')) {
            $exists = DB::table('subjects')->where('code','CIV')->first();
            if (!$exists) {
                $payload = [];
                foreach ([
                    'name' => 'Civics',
                    'code' => 'CIV',
                    'school_code' => $schoolCode,
                ] as $k=>$v) { if (Schema::hasColumn('subjects',$k)) $payload[$k] = $v; }
                if (!empty($payload)) DB::table('subjects')->insert($payload);
            }
        }
    }
}
