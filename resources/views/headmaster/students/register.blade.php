@extends('layouts.headmaster')

@section('title','Student Registration | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  @if (session('error'))
    <div class="mb-4 p-3 rounded bg-amber-50 text-amber-800 ring-1 ring-amber-200">{{ session('error') }}</div>
  @endif
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Student Registration</h1>
    <a href="{{ route('headmaster.students.index') }}" class="px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90 flex items-center gap-2">
      <i class="fa-solid fa-list"></i>
      <span>Back to List</span>
    </a>
  </div>

  <div class="bg-white rounded ring-1 ring-slate-200">
    <div class="border-b px-4">
      <nav class="flex gap-4" aria-label="Registration methods">
        <button type="button" class="tab-btn py-3 border-b-2 -mb-px px-1 font-medium border-emas-green text-emas-green" data-target="#tab-manual">Manual</button>
        <button type="button" class="tab-btn py-3 border-b-2 -mb-px px-1 font-medium border-transparent text-slate-500 hover:text-slate-700" data-target="#tab-bulk">Bulk Upload</button>
      </nav>
    </div>

    <div class="p-4">
      {{-- Manual Registration --}}
      <section id="tab-manual">
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4" method="POST" action="{{ route('headmaster.students.store') }}">
          @csrf
          <div class="md:col-span-2">
            <label class="block text-sm text-slate-600 mb-1">Full Name</label>
            <input name="full_name" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g. Asha Juma" required>
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Admission Number</label>
            @if(!empty($schoolCode))
              <div class="mb-1 text-xs text-slate-600">School Code: <span class="px-2 py-0.5 rounded-full bg-emas-green/10 text-emas-green ring-1 ring-emas-green/30">S.{{ $schoolCode }}</span></div>
            @endif
            <div class="flex gap-2">
              <input id="admNo" name="admission_number" type="text" class="flex-1 border rounded px-3 py-2" placeholder="e.g. S.SCH123.0001">
              <button type="button" id="btnGenAdm" class="px-3 py-2 border rounded hover:bg-slate-50" data-next="{{ $nextAdmission }}">
                <i class="fa-solid fa-hashtag"></i> Generate
              </button>
            </div>
            <p class="text-xs text-slate-500 mt-1">Format: S.{schoolcode}.NNNN. Generate suggests the next number for your school.</p>
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Gender</label>
            <select name="gender" class="w-full border rounded px-3 py-2" required>
              <option value="">Select</option>
              <option value="F">Female</option>
              <option value="M">Male</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Class</label>
            <select name="class" class="w-full border rounded px-3 py-2" required>
              <option value="">Select</option>
              <option>Form I</option>
              <option>Form II</option>
              <option>Form III</option>
              <option>Form IV</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm text-slate-600 mb-2">Subjects</label>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2">
              @foreach(($subjects ?? []) as $sub)
                <label class="inline-flex items-center gap-2 px-3 py-2 border rounded">
                  <input name="subjects[]" value="{{ $sub }}" type="checkbox" class="accent-emas-green">
                  <span>{{ $sub }}</span>
                </label>
              @endforeach
            </div>
          </div>
          <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button class="px-4 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">Save Student</button>
          </div>
        </form>
      </section>

      {{-- Bulk Upload --}}
      <section id="tab-bulk" class="hidden">
        <div class="grid md:grid-cols-2 gap-4">
          <div class="p-4 rounded ring-1 ring-slate-200">
            <h3 class="font-semibold mb-2">1) Download Template</h3>
            <p class="text-sm text-slate-600 mb-3">Choose the class to download the correct CSV template.</p>
            <div class="flex flex-wrap gap-2">
              <a href="{{ route('headmaster.students.template','form-ii') }}" class="px-3 py-2 border rounded hover:bg-slate-50">Download Form II CSV</a>
              <a href="{{ route('headmaster.students.template','form-iv') }}" class="px-3 py-2 border rounded hover:bg-slate-50">Download Form IV CSV</a>
              <a href="{{ route('headmaster.students.template_excel','form-ii') }}" class="px-3 py-2 border rounded hover:bg-slate-50">Download Form II Excel</a>
              <a href="{{ route('headmaster.students.template_excel','form-iv') }}" class="px-3 py-2 border rounded hover:bg-slate-50">Download Form IV Excel</a>
            </div>
            <p class="text-xs text-slate-500 mt-2">Form II: all subjects (core + options) allowed. Form IV: 7 core mandatory + chosen options.</p>
          </div>
          <div class="p-4 rounded ring-1 ring-slate-200">
            <h3 class="font-semibold mb-2">2) Upload Filled File</h3>
            <p class="text-sm text-slate-600 mb-3">Upload your completed CSV to add multiple students.</p>
            <form method="POST" action="{{ route('headmaster.students.bulk_upload') }}" enctype="multipart/form-data">
              @csrf
              <input name="file" type="file" accept=".csv,.xlsx" class="block w-full mb-3" required>
              <button class="px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">Upload</button>
            </form>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script>
    // Simple tabs for methods
    (function(){
      const root = document.currentScript.closest('.bg-white');
      if (!root) return;
      const buttons = root.querySelectorAll('.tab-btn');
      buttons.forEach(btn => btn.addEventListener('click', () => {
        buttons.forEach(b => { b.classList.remove('text-emas-green','border-emas-green'); b.classList.add('text-slate-500','border-transparent'); });
        btn.classList.add('text-emas-green','border-emas-green');
        root.querySelectorAll('section[id^="tab-"]').forEach(s => s.classList.add('hidden'));
        const target = root.querySelector(btn.getAttribute('data-target'));
        if (target) target.classList.remove('hidden');
      }));
    })();

    // Admission number generator
    (function(){
      const genBtn = document.getElementById('btnGenAdm');
      const input = document.getElementById('admNo');
      if (!genBtn || !input) return;
      const pad = (n, w=4) => String(n).padStart(w, '0');
      const inc = (v) => {
        const m = v.match(/(\d+)(?!.*\d)/);
        if (!m) return v; // no digits to increment
        const n = parseInt(m[1], 10) + 1;
        return v.replace(m[1], pad(n, m[1].length));
      };
      genBtn.addEventListener('click', () => {
        const next = genBtn.getAttribute('data-next') || 'S.SCHOOL.0001';
        if (!input.value) {
          input.value = next;
        } else {
          input.value = inc(input.value);
        }
        input.focus();
      });
    })();
  </script>
</div>
@endsection
