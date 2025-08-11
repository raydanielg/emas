@extends('layouts.headmaster')

@section('title','Teachers | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-chalkboard-user"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Teachers</h1>
        <div class="text-slate-500 text-sm">Manage teachers and their subject assignments</div>
      </div>
    </div>
    <button onclick="openAddTeacher()" class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
      <i class="fa-solid fa-user-plus"></i>
      <span>Add Teacher</span>
    </button>
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

  @if($teachers->count() === 0)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-8 text-center">
      <div class="mx-auto h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-500"><i class="fa-solid fa-chalkboard-user"></i></div>
      <div class="mt-2 font-semibold">No teachers found</div>
      <div class="text-slate-500 text-sm">Click "Add Teacher" to create the first one.</div>
    </div>
  @else
  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-slate-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Name</th>
          <th class="text-left font-semibold px-4 py-2">Phone</th>
          <th class="text-left font-semibold px-4 py-2">Bank No.</th>
          <th class="text-left font-semibold px-4 py-2">Subject</th>
          <th class="text-left font-semibold px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($teachers as $t)
          @php($tname = $t->display_name)
          <tr class="border-t">
            <td class="px-4 py-2">
              <div class="font-medium">{{ $tname }}</div>
              <div class="text-xs text-slate-500">ID: {{ $t->id }}</div>
            </td>
            <td class="px-4 py-2">{{ $t->phone ?? '—' }}</td>
            <td class="px-4 py-2">{{ $t->bank_number ?? '—' }}</td>
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                <select class="border rounded px-2 py-1 text-sm" onchange="assignSubject({{ $t->id }}, this.value)">
                  <option value="">-- Select Subject --</option>
                  @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ (int)($t->subject_assigned_id ?? 0) === (int)$s->id ? 'selected' : '' }}>{{ $s->name }} @if(!empty($s->code)) ({{ $s->code }}) @endif</option>
                  @endforeach
                </select>
                <span id="ind-{{ $t->id }}" class="text-xs text-slate-500"></span>
              </div>
            </td>
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-1 px-2 py-1 text-xs border rounded hover:bg-slate-50" title="View"><i class="fa-regular fa-eye"></i><span>View</span></button>
                {{-- Optional: Add delete/edit in future --}}
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- Add Teacher Modal --}}
<div id="add-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeAddTeacher()"></div>
  <div class="relative mx-auto mt-16 w-full max-w-2xl">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-6 shadow-lg">
      <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-full bg-emas-green/10 text-emas-green flex items-center justify-center"><i class="fa-solid fa-user-plus"></i></div>
        <div>
          <div class="text-lg font-semibold">Add Teacher</div>
          <div class="text-slate-500 text-sm">Fill in the details below</div>
        </div>
      </div>
      <form method="post" action="{{ route('headmaster.teachers.store') }}" onsubmit="setAddLoading(true)">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm mb-1">First Name</label>
            <input type="text" name="first_name" class="border rounded px-3 py-2 w-full" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Middle Name</label>
            <input type="text" name="middle_name" class="border rounded px-3 py-2 w-full">
          </div>
          <div>
            <label class="block text-sm mb-1">Last Name</label>
            <input type="text" name="last_name" class="border rounded px-3 py-2 w-full" required>
          </div>
          <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm mb-1">Phone</label>
              <input type="text" name="phone" class="border rounded px-3 py-2 w-full" placeholder="07xx/06xx...">
            </div>
            <div>
              <label class="block text-sm mb-1">Bank Number</label>
              <input type="text" name="bank_number" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
              <label class="block text-sm mb-1">Email (optional)</label>
              <input type="email" name="email" class="border rounded px-3 py-2 w-full" placeholder="teacher@example.com">
            </div>
          </div>
          <div class="sm:col-span-3">
            <label class="block text-sm mb-1">Assign Subject (optional)</label>
            <select name="subject_id" class="border rounded px-3 py-2 w-full">
              <option value="">-- None --</option>
              @foreach($subjects as $s)
                <option value="{{ $s->id }}">{{ $s->name }} @if(!empty($s->code)) ({{ $s->code }}) @endif</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button type="button" class="px-3 py-2 border rounded hover:bg-slate-50" onclick="closeAddTeacher()">Cancel</button>
          <button id="add-btn" class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
            <span class="add-icon"><i class="fa-solid fa-floppy-disk"></i></span>
            <span class="add-text">Save</span>
            <span class="add-loading hidden"><i class="fa-solid fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openAddTeacher(){ document.getElementById('add-modal').classList.remove('hidden'); }
  function closeAddTeacher(){ document.getElementById('add-modal').classList.add('hidden'); setAddLoading(false); }
  function setAddLoading(state){
    const btn = document.getElementById('add-btn');
    const icon = btn.querySelector('.add-icon');
    const txt = btn.querySelector('.add-text');
    const load = btn.querySelector('.add-loading');
    if(state){ btn.setAttribute('disabled','disabled'); icon.classList.add('hidden'); txt.textContent='Saving...'; load.classList.remove('hidden'); }
    else { btn.removeAttribute('disabled'); icon.classList.remove('hidden'); txt.textContent='Save'; load.classList.add('hidden'); }
  }
  function assignSubject(teacherId, subjectId){
    const ind = document.getElementById('ind-'+teacherId);
    ind.textContent = 'Saving...';
    fetch('{{ route('headmaster.teachers.assign_subject', 0) }}'.replace('/0/','/'+teacherId+'/'),{
      method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
      body: JSON.stringify({subject_id: subjectId})
    }).then(r=>r.json()).then(d=>{
      ind.textContent = d.ok ? 'Saved' : (d.message || 'Error');
      setTimeout(()=>{ ind.textContent=''; }, 1200);
    }).catch(()=>{ ind.textContent = 'Error'; setTimeout(()=>{ ind.textContent=''; }, 1500); });
  }
</script>
@endsection
