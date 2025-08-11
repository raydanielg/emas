@extends('layouts.user')

@section('title','Registration Report | eMAS')

@section('content')
<div class="space-y-4">
  <h1 class="text-xl font-semibold">Registration Summary</h1>
  <div class="bg-white rounded-xl ring-1 ring-gray-200 overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-4 py-2">School</th>
          <th class="text-left px-4 py-2">Ward</th>
          <th class="text-left px-4 py-2">District</th>
          <th class="text-left px-4 py-2">Region</th>
          <th class="text-right px-4 py-2">Female</th>
          <th class="text-right px-4 py-2">Male</th>
          <th class="text-right px-4 py-2">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($schools as $s)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $s->name }}</td>
            <td class="px-4 py-2">{{ $s->ward }}</td>
            <td class="px-4 py-2">{{ $s->district }}</td>
            <td class="px-4 py-2">{{ $s->region }}</td>
            <td class="px-4 py-2 text-right">{{ number_format($female[$s->id] ?? 0) }}</td>
            <td class="px-4 py-2 text-right">{{ number_format($male[$s->id] ?? 0) }}</td>
            <td class="px-4 py-2 text-right font-semibold">{{ number_format($counts[$s->id] ?? 0) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
