@extends('layouts.headmaster')

@section('title','Manage Institution | Headmaster')

@section('content')
@php
  $schools = $schools ?? [];
  $selectedCode = $selectedCode ?? null;
  $details = $details ?? [];
  $canManage = $canManage ?? false;
@endphp
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-sky-50 text-sky-700 grid place-items-center">
        <i class="fa-solid fa-gear"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Manage Institution</h1>
        <div class="text-slate-500 text-sm">View or edit school details @if(!$canManage)<span class="text-amber-600">(read-only)</span>@endif</div>
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

  <div class="bg-white rounded-xl ring-1 ring-slate-200 overflow-hidden">
    <div class="p-4 border-b text-slate-700 font-medium flex items-center justify-between">
      <div>School Details</div>
      @if($selectedCode)
        <div class="text-xs text-slate-500">Code: {{ $selectedCode }}</div>
      @endif
    </div>
    <div class="p-4">
      <form method="post" action="#" onsubmit="return false;" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @php
          $fields = [
            'name' => 'School Name',
            'principal_name' => 'Principal Name',
            'principal_phone' => 'Principal Phone',
            'ownership' => 'Ownership',
            'region' => 'Region',
            'district' => 'District',
            'postal_address' => 'Postal Address',
          ];
        @endphp
        @foreach($fields as $key=>$label)
          <div>
            <label class="block text-sm text-slate-600 mb-1">{{ $label }}</label>
            <input type="text" value="{{ $details[$key] ?? '' }}" class="w-full px-3 py-2 rounded-md ring-1 ring-slate-300 focus:ring-indigo-500" @if(!$canManage) disabled @endif>
          </div>
        @endforeach
        <div class="md:col-span-2 flex items-center justify-end gap-3 mt-2">
          @if($canManage)
          <button class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700" type="button" onclick="alert('Saving will be wired when admin grants API endpoint.');">Save Changes</button>
          @else
          <span class="text-xs text-slate-500">You do not have permission to edit.</span>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
