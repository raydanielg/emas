@extends('layouts.headmaster')

@section('title','Request Details | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-start justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Request #{{ $item->id ?? '' }}</h1>
      <p class="text-slate-500">Details for this request.</p>
    </div>
    <a href="{{ route('headmaster.reports.requests.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50">
      <i class="fa-solid fa-arrow-left"></i>
      <span>Back</span>
    </a>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-5 space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <div class="text-xs text-slate-500">Type</div>
        <div class="font-medium">{{ $item->type ?? 'student_count' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Quantity</div>
        <div class="font-medium">{{ $item->quantity ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">School</div>
        <div class="font-medium">{{ $item->school_name ?? '' }} @if(!empty($item->school_code))<span class="text-slate-500">({{ $item->school_code }})</span>@endif</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Status</div>
        <div>
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs
            @if(($item->status ?? '')==='approved') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
            @elseif(($item->status ?? '')==='need_approval') bg-amber-50 text-amber-700 ring-1 ring-amber-200
            @elseif(($item->status ?? '')==='cancelled') bg-rose-50 text-rose-700 ring-1 ring-rose-200
            @else bg-slate-100 text-slate-700 ring-1 ring-slate-200 @endif">{{ $item->status ?? '-' }}</span>
        </div>
      </div>
    </div>

    @if(!empty($item->comment))
    <div>
      <div class="text-xs text-slate-500">Comment</div>
      <div class="whitespace-pre-wrap">{{ $item->comment }}</div>
    </div>
    @endif

    @if(!empty($item->meta))
      @php
        $meta = json_decode($item->meta ?? '{}', true);
      @endphp
      @if(!empty($meta['student_ids']))
      <div>
        <div class="text-xs text-slate-500 mb-1">Selected Students</div>
        <code class="text-xs">{{ implode(', ', $meta['student_ids']) }}</code>
      </div>
      @endif
    @endif

    <div class="text-xs text-slate-500">Created: {{ isset($item->created_at) ? (\Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i')) : '-' }}</div>
  </div>
</div>
@endsection
