@extends('layouts.headmaster')

@section('title','Edit Subject | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-pen-to-square"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Edit Subject</h1>
        <div class="text-slate-500 text-sm">Update subject details and assignment</div>
      </div>
    </div>
    <a href="{{ route('headmaster.subjects.show', $subject->id) }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
  </div>

  @if(session('error'))
    <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 border border-red-200">
      <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
  @endif

  <div class="bg-white rounded-xl p-5 ring-1 ring-slate-200">
    <form method="post" action="{{ route('headmaster.subjects.update', $subject->id) }}">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm mb-1">Name</label>
          <input type="text" name="name" value="{{ $subject->name ?? '' }}" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-sm mb-1">Code</label>
          <input type="text" name="code" value="{{ $subject->code ?? '' }}" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm mb-1">Assign Teacher (optional)</label>
          <select name="teacher_id" class="border rounded px-3 py-2 w-full">
            <option value="">-- None --</option>
            @foreach($teachers as $t)
              <option value="{{ $t->id }}" {{ (int)($subject->teacher_id ?? 0) === (int)$t->id ? 'selected' : '' }}>{{ $t->display_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="mt-5 flex items-center gap-2 justify-end">
        <a href="{{ route('headmaster.subjects.show', $subject->id) }}" class="px-3 py-2 border rounded hover:bg-slate-50">Cancel</a>
        <button class="px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
