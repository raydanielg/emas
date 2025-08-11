@extends('layouts.headmaster')

@section('title','Selected for Marking | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-clipboard-check"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Selected for Marking</h1>
        <div class="text-slate-500 text-sm">View selections created based on results submitted</div>
      </div>
    </div>
    <div class="text-sm text-slate-500">Latest first</div>
  </div>

  @if(session('success'))
    <div class="mb-3 px-3 py-2 rounded bg-green-50 text-green-700 border border-green-200">
      <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 border border-red-200">
      <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
  @endif

  @if($selections->count() === 0)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-8 text-center">
      <div class="mx-auto h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-500"><i class="fa-solid fa-clipboard-check"></i></div>
      <div class="mt-2 font-semibold">No selections yet</div>
      <div class="text-slate-500 text-sm">Selections will appear here after results are processed.</div>
    </div>
  @else
  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-slate-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Title</th>
          <th class="text-left font-semibold px-4 py-2">Created</th>
          <th class="text-left font-semibold px-4 py-2">Status</th>
          <th class="text-left font-semibold px-4 py-2">Counts</th>
          <th class="text-left font-semibold px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($selections as $s)
        <tr class="border-t">
          <td class="px-4 py-2">
            <div class="font-medium">{{ $s->title ?? ('Selection #'.$s->id) }}</div>
            @if(!empty($s->notes))
              <div class="text-xs text-slate-500 line-clamp-1">{{ $s->notes }}</div>
            @endif
          </td>
          <td class="px-4 py-2 text-slate-600">{{ isset($s->created_at) ? \Carbon\Carbon::parse($s->created_at)->diffForHumans() : '—' }}</td>
          <td class="px-4 py-2">
            @php($st = strtolower($s->status ?? 'pending'))
            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full @if($st==='finalized') bg-green-50 text-green-700 @else bg-amber-50 text-amber-700 @endif">
              <i class="fa-solid @if($st==='finalized') fa-circle-check @else fa-hourglass-half @endif"></i>
              <span class="capitalize">{{ $st }}</span>
            </span>
          </td>
          <td class="px-4 py-2 text-slate-600">
            <div class="flex items-center gap-3 text-xs">
              <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-group text-slate-400"></i> {{ $s->count_total ?? 0 }}</span>
              <span class="inline-flex items-center gap-1 text-green-700"><i class="fa-solid fa-circle-check"></i> {{ $s->count_selected ?? 0 }}</span>
              <span class="inline-flex items-center gap-1 text-amber-700"><i class="fa-solid fa-hourglass-half"></i> {{ $s->count_pending ?? 0 }}</span>
              <span class="inline-flex items-center gap-1 text-red-700"><i class="fa-solid fa-circle-xmark"></i> {{ $s->count_rejected ?? 0 }}</span>
            </div>
          </td>
          <td class="px-4 py-2">
            <div class="flex items-center gap-2">
              <a href="{{ route('headmaster.teachers.selected.show', $s->id) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs border rounded hover:bg-slate-50"><i class="fa-regular fa-eye"></i><span>View</span></a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
