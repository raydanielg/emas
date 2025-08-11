@extends('layouts.headmaster')

@section('title','Student Profile | Headmaster')

@section('content')
<div class="max-w-4xl mx-auto">
  <a href="{{ route('headmaster.students.index') }}" class="inline-flex items-center gap-2 mb-4 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
    <span class="i-mdi-arrow-left"></span> Back to Students
  </a>

  <div class="bg-white rounded-lg ring-1 ring-slate-200 p-5">
    <div class="flex gap-6">
      <div>
        @php $img = (isset($student->photo_path) && $student->photo_path) ? (\Illuminate\Support\Str::startsWith($student->photo_path,'http') ? $student->photo_path : asset('storage/'.$student->photo_path)) : asset('avatars/default.png'); @endphp
        <img src="{{ $img }}" alt="" class="w-28 h-28 rounded object-cover ring-1 ring-slate-200">
        <form action="{{ route('headmaster.students.upload_image', $student->id) }}" method="post" enctype="multipart/form-data" class="mt-2">
          @csrf
          <input type="file" name="photo" accept="image/*" class="block text-xs mb-2">
          <button class="px-3 py-1.5 border rounded bg-slate-50 hover:bg-white">Upload Photo</button>
        </form>
      </div>
      @php($displayName = $student->name ?? $student->full_name ?? $student->student_name ?? $student->admission_number ?? ('ID '.$student->id))
      <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div><div class="text-xs text-slate-500">Name</div><div class="font-semibold">{{ $displayName }}</div></div>
        <div><div class="text-xs text-slate-500">Admission No</div><div class="font-semibold">{{ $student->admission_number }}</div></div>
        <div><div class="text-xs text-slate-500">Class</div><div class="font-semibold">{{ $student->class ?? '-' }}</div></div>
        <div><div class="text-xs text-slate-500">Gender</div><div class="font-semibold">{{ $student->gender ?? '-' }}</div></div>
        <div class="sm:col-span-2"><div class="text-xs text-slate-500">Subjects</div>
          @if (!empty($student->subjects_list))
            <div class="flex flex-wrap gap-2 mt-1">
              @foreach($student->subjects_list as $subj)
                <span class="px-2 py-1 rounded-full text-xs bg-slate-50 ring-1 ring-slate-200">{{ $subj }}</span>
              @endforeach
            </div>
          @else
            <div class="text-slate-500">No subjects assigned.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
