@extends('layouts.headmaster')

@section('title','Institution Profile | Headmaster')

@section('content')
@php
  $schools = $schools ?? [];
  $selectedCode = $selectedCode ?? null;
  $details = $details ?? [];
  $studentsCount = $studentsCount ?? 0;
  $teachersCount = $teachersCount ?? 0;
  $subjectsCount = $subjectsCount ?? 0;
  $classesCount = $classesCount ?? 0;
  $headmasterName = $headmasterName ?? null;
  $region = $region ?? null;
  $district = $district ?? null;
@endphp
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-slate-50 text-slate-700 grid place-items-center">
        <i class="fa-solid fa-school"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Institution Profile</h1>
        <div class="text-slate-500 text-sm">View your school details</div>
      </div>
    </div>
  </div>

  @if(count($schools) > 1)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4 mb-5">
      <form method="get" class="flex items-center gap-3">
        <label class="text-sm text-slate-600">Select School</label>
        <select name="school" class="px-3 py-2 rounded-md ring-1 ring-slate-300 focus:ring-indigo-500 focus:outline-none">
          @foreach($schools as $s)
            <option value="{{ $s['code'] }}" @selected($selectedCode===$s['code'])>{{ $s['name'] }} ({{ $s['code'] }})</option>
          @endforeach
        </select>
        <button class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Apply</button>
      </form>
    </div>
  @endif

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Students</div>
      <div class="text-2xl font-bold">{{ number_format((int)$studentsCount) }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Teachers</div>
      <div class="text-2xl font-bold">{{ number_format((int)$teachersCount) }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Subjects</div>
      <div class="text-2xl font-bold">{{ number_format((int)$subjectsCount) }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Classes</div>
      <div class="text-2xl font-bold">{{ number_format((int)$classesCount) }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Headmaster</div>
      <div class="text-base font-semibold text-slate-800">{{ $headmasterName ?: '—' }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-3">
      <div class="text-xs text-slate-500">Location</div>
      <div class="text-base font-semibold text-slate-800">{{ trim(($district ? $district.', ' : '').($region ?: '')) ?: '—' }}</div>
    </div>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-slate-200 overflow-hidden">
    <div class="p-4 border-b text-slate-700 font-medium flex items-center justify-between">
      <div>School Details</div>
      @if($selectedCode)
        <div class="text-xs text-slate-500">Code: {{ $selectedCode }}</div>
      @endif
    </div>
    <div class="p-4">
      @if(!empty($details))
        @php
          $val = function(array $keys) use ($details) {
            foreach ($keys as $k) { if (isset($details[$k]) && $details[$k] !== '') return $details[$k]; }
            return null;
          };
          $rows = [
            ['School Name', $val(['name','school_name','centre_name'])],
            ['Registration Code', $selectedCode ?? $val(['code','school_code','emis_code','registration_no','reg_no'])],
            ['Ownership', $val(['ownership','owner','category'])],
            ['Region', $val(['region','school_region','province'])],
            ['District', $val(['district','council','lga'])],
            ['Ward', $val(['ward','division'])],
            ['Phone', $val(['phone','contact_phone','tel'])],
            ['Email', $val(['email','contact_email'])],
            ['Postal Address', $val(['postal_address','po_box'])],
            ['Physical Address', $val(['address','location'])],
            ['Level', $val(['level','type'])],
            ['Website', $val(['website','site'])],
            ['Established', $val(['established','established_year','year_started'])],
          ];
          $hasAny = collect($rows)->contains(fn($r)=>!empty($r[1]));
        @endphp
        @if($hasAny)
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <tbody class="divide-y divide-slate-100">
              @foreach($rows as [$label,$value])
                @if(!empty($value))
                <tr>
                  <td class="py-2 pr-6 text-slate-500 whitespace-nowrap">{{ $label }}</td>
                  <td class="py-2 text-slate-800">{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
        @endif

        <details class="mt-4">
          <summary class="cursor-pointer text-sm text-slate-600 hover:text-slate-800">Show all raw fields</summary>
          <div class="mt-3">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
              @foreach($details as $k=>$v)
                <div class="flex items-start">
                  <dt class="w-48 text-slate-500 text-sm capitalize">{{ str_replace(['_','-'],' ', $k) }}</dt>
                  <dd class="flex-1 text-slate-800">{{ is_scalar($v) ? $v : json_encode($v) }}</dd>
                </div>
              @endforeach
            </dl>
          </div>
        </details>
      @else
        <div class="text-slate-500">No details available for this school.</div>
      @endif
    </div>
  </div>
</div>
@endsection
