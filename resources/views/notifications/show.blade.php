@extends('layouts.user')
@section('title', $n->title)

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold">{{ $n->title }}</h1>
    <a href="{{ route('notifications.index') }}" class="text-sm text-emas-green hover:underline">Back to inbox</a>
  </div>

  <div class="bg-white ring-1 ring-gray-200 rounded-md p-4 space-y-3">
    <div class="text-xs text-slate-500 flex items-center gap-2">
      <span>Created: {{ \Carbon\Carbon::parse($n->created_at)->format('M d, Y H:i') }}</span>
      <span>•</span>
      @if(!$n->read_at)
        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Unread</span>
      @else
        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Read {{ \Carbon\Carbon::parse($n->read_at)->format('M d, Y H:i') }}</span>
      @endif
    </div>

    @if($n->message)
      <div class="prose max-w-none">
        {!! nl2br(e($n->message)) !!}
      </div>
    @endif

    @if($n->link_url)
      <div>
        <a href="{{ $n->link_url }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-emas-green text-white">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Open link
        </a>
      </div>
    @endif
  </div>
</div>
@endsection
