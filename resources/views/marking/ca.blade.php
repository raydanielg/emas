@extends('layouts.user')
@section('title','Continuous Assessments | Marking | eMAS')
@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Continuous Assessments (CA)</h1>
    <div class="flex items-center gap-2">
      <input type="text" placeholder="Search" class="px-3 py-2 rounded-lg border border-gray-200 text-sm" />
      <select class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
        <option>2…</option>
      </select>
      <select class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
        <option>Form I</option>
        <option>Form II</option>
        <option>Form III</option>
        <option>Form IV</option>
      </select>
      <button class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm">Template</button>
      <button class="px-3 py-2 rounded-lg bg-emas-green text-white text-sm">Upload</button>
      <button class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm">Reports</button>
    </div>
  </div>

  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-gray-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Student Name</th>
          <th class="text-left font-semibold px-4 py-2">Test One</th>
          <th class="text-left font-semibold px-4 py-2">Mid Term</th>
          <th class="text-left font-semibold px-4 py-2">Two</th>
          <th class="text-left font-semibold px-4 py-2">Terminal</th>
          <th class="text-left font-semibold px-4 py-2">Test One2</th>
          <th class="text-left font-semibold px-4 py-2">Mid Term2</th>
          <th class="text-left font-semibold px-4 py-2">Test Two2</th>
          <th class="text-left font-semibold px-4 py-2">Annual</th>
          <th class="text-left font-semibold px-4 py-2">Project</th>
        </tr>
      </thead>
      <tbody>
        @for ($i=0; $i<12; $i++)
        <tr class="border-t">
          <td class="px-4 py-2">ABIGALE DEMO {{$i}}</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
          <td class="px-4 py-2">—</td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>
</div>
@endsection
