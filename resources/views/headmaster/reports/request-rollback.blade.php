@extends('layouts.headmaster')

@section('title','Rollback Request | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Rollback Request</h1>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
    <form method="POST" action="{{ route('headmaster.reports.requests.rollback.store') }}" class="space-y-5">
      @csrf

      @if(($schools ?? collect())->count())
      <div>
        <label class="block text-sm font-medium text-slate-700">School</label>
        <select name="school_id" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green">
          <option value="">— Select school (optional) —</option>
          @foreach($schools as $s)
            <option value="{{ $s->id }}">{{ $s->name }} @if(!empty($s->code)) ({{ $s->code }}) @endif</option>
          @endforeach
        </select>
      </div>
      @endif

      <div>
        <label class="block text-sm font-medium text-slate-700">Target (optional)</label>
        <input type="text" name="target" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" placeholder="e.g. Admission No, Batch ID, or Reference" />
        <p class="mt-1 text-xs text-slate-500">Specify what should be rolled back if applicable.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700">Reason</label>
        <textarea name="reason" rows="4" required class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" placeholder="Describe the problem and why rollback is needed..."></textarea>
      </div>

      <div class="pt-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-emas-green text-white hover:bg-emerald-600">
          <i class="fa-solid fa-paper-plane"></i>
          <span>Submit for Approval</span>
        </button>
        <a href="{{ route('headmaster.reports.index') }}" class="ml-3 inline-flex items-center gap-2 px-3 py-2 rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back</span>
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
