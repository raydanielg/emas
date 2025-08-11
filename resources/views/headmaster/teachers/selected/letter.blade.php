@extends('layouts.headmaster')

@section('title','Official Letter | Headmaster')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4 print:hidden">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-regular fa-file-lines"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Official Letter</h1>
        <div class="text-slate-500 text-sm">Selection #{{ $selection->id }} @if($selection->letter_generated_at) • Generated {{ \Carbon\Carbon::parse($selection->letter_generated_at)->diffForHumans() }} @endif</div>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('headmaster.teachers.selected.show', $selection->id) }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
      <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90"><i class="fa-solid fa-print"></i><span>Print</span></button>
    </div>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-6">
    <style>
      @media print { .print\:hidden{ display:none !important } body{ background:#fff } }
      .letter h1,.letter h2{ margin:0; }
      .letter table{ border-collapse: collapse; width:100%; font-size:13px }
      .letter th,.letter td{ border:1px solid #ddd; padding:6px; }
      .letter thead tr{ background:#f2f2f2 }
    </style>
    <div class="letter prose max-w-none">
      {!! $selection->letter_html !!}
    </div>
  </div>
</div>
@endsection
