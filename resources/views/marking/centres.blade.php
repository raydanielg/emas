@extends('layouts.user')
@section('title','Centres | Marking | eMAS')
@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Centres</h1>
    <form method="get" action="{{ route('marking.centres') }}" class="flex items-center gap-2" data-show-loader>
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/code/region" class="px-3 py-2 rounded-lg border border-gray-200 text-sm" />
      <button class="px-3 py-2 rounded-lg bg-emas-green text-white text-sm">Search</button>
    </form>
  </div>

  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-gray-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Code</th>
          <th class="text-left font-semibold px-4 py-2">Name</th>
          <th class="text-left font-semibold px-4 py-2">Ward</th>
          <th class="text-left font-semibold px-4 py-2">District</th>
          <th class="text-left font-semibold px-4 py-2">Region</th>
          <th class="text-left font-semibold px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($schools as $s)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $s->code }}</td>
          <td class="px-4 py-2 text-emas-green">{{ $s->name }}</td>
          <td class="px-4 py-2">{{ $s->ward }}</td>
          <td class="px-4 py-2">{{ $s->district }}</td>
          <td class="px-4 py-2">{{ $s->region }}</td>
          <td class="px-4 py-2 text-slate-500">
            <a href="{{ route('marking.centres.sheet', ['school' => $s->id]) }}" class="hover:text-slate-700 show-loader" title="View">👁️</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No centres assigned.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
