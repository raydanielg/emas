<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiltersController extends Controller
{
    public function regions()
    {
        $regions = DB::table('regions')->select('id','name')->orderBy('name')->get();
        return response()->json($regions);
    }

    public function districts(Request $request)
    {
        $regionId = $request->query('region_id');
        $q = DB::table('districts')->select('id','name');
        if ($regionId) { $q->where('region_id', $regionId); }
        return response()->json($q->orderBy('name')->get());
    }

    public function schools(Request $request)
    {
        $districtId = $request->query('district_id');
        $q = DB::table('schools')->select('id','name','code','ward_id');
        if ($districtId) {
            $q->whereIn('ward_id', function($sub) use ($districtId) {
                $sub->from('wards')->select('id')->where('district_id', $districtId);
            });
        }
        return response()->json($q->orderBy('name')->limit(500)->get());
    }

    public function forms(Request $request)
    {
        // Static for now; adjust to your schema if forms are in DB
        $forms = [
            ['id' => 'all', 'name' => 'All forms'],
            ['id' => 'I', 'name' => 'Form I'],
            ['id' => 'II', 'name' => 'Form II'],
            ['id' => 'III', 'name' => 'Form III'],
            ['id' => 'IV', 'name' => 'Form IV'],
        ];
        return response()->json($forms);
    }

    public function saveSelection(Request $request)
    {
        $data = $request->validate([
            'region_id' => ['nullable','integer'],
            'district_id' => ['nullable','integer'],
            'school_id' => ['nullable','integer'],
            'form' => ['nullable','string','max:20'],
        ]);
        session(['filters' => $data]);
        return response()->json(['ok' => true, 'filters' => $data]);
    }
}
