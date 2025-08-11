@extends('layouts.headmaster')

@section('title','Assign Student Subjects | Headmaster')

@section('content')
<style>
.lds-ripple, .lds-ripple div { box-sizing: border-box; }
.lds-ripple { display:inline-block; position:relative; width:24px; height:24px; color:#10b981; }
.lds-ripple div { position:absolute; border:3px solid currentColor; opacity:1; border-radius:50%; animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; }
.lds-ripple div:nth-child(2) { animation-delay: -0.5s; }
@keyframes lds-ripple { 0% { top:10px; left:10px; width:4px; height:4px; opacity:0;} 4.9% { top:10px; left:10px; width:4px; height:4px; opacity:0;} 5% { top:10px; left:10px; width:4px; height:4px; opacity:1;} 100% { top:0; left:0; width:24px; height:24px; opacity:0; } }
.subject-chip { display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:6px; font-size:12px; border:1px solid #cbd5e1; color:#334155; background:#f1f5f9; cursor:pointer; }
.subject-chip.active { border-color:#10b981; color:#065f46; background:#ecfdf5; }
.save-indicator { display:none; align-items:center; gap:6px; color:#10b981; font-size:12px; }
.save-indicator.show { display:inline-flex; }
</style>

<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Student Subjects</h1>
      <div class="text-slate-500 text-sm">Assign by clicking subject codes. Changes auto-save.</div>
    </div>
    <a href="{{ route('headmaster.students.register') }}" class="px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">Register</a>
  </div>

  <div class="bg-white rounded-lg p-4 ring-1 ring-slate-200">
    @if(collect($students)->isEmpty())
      <div class="py-16 text-center">
        <div class="text-lg font-semibold mb-2">Hakuna wanafunzi kwa mkuu huyu kwa sasa</div>
        <div class="text-slate-500 mb-6">Tafadhali sajili wanafunzi au pakiwa na tatizo la mgawanyo wa shule, wasiliana na msimamizi.</div>
        <a href="{{ route('headmaster.students.register') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
          <i class="fa-solid fa-user-plus"></i>
          <span>Sajili Mwanafunzi</span>
        </a>
      </div>
    @else
    <div class="mb-3 flex items-center gap-3">
      <input id="search" type="text" placeholder="Search student" class="border rounded px-3 py-2 w-72">
      <button class="px-3 py-2 border rounded" onclick="filterRows()">Search</button>
      <div class="ml-auto text-slate-500 text-sm">Showing {{ count($students) }} students</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-600">
            <th class="py-2 pr-3">Student</th>
            <th class="py-2 pr-3">Subjects</th>
            <th class="py-2 pr-3">Status</th>
            <th class="py-2 pr-3">Actions</th>
          </tr>
        </thead>
        <tbody id="rows">
          @foreach($students as $s)
            @php
              $current = [];
              if (isset($s->subjects) && is_string($s->subjects)) {
                $dec = json_decode($s->subjects, true);
                $current = is_array($dec) ? $dec : [];
              }
              $displayName = $s->name ?? $s->full_name ?? $s->student_name ?? $s->admission_number ?? ('ID '.$s->id);
            @endphp
            <tr class="border-t" data-name="{{ strtoupper($displayName) }}">
              <td class="py-2 pr-3 font-medium">{{ $displayName }}</td>
              <td class="py-2 pr-3">
                <div class="flex flex-wrap gap-2" data-student="{{ $s->id }}">
                  @foreach($catalog as $code)
                    <span class="subject-chip {{ in_array($code, $current, true) ? 'active' : '' }}" data-code="{{ $code }}">{{ $code }}</span>
                  @endforeach
                </div>
                <span class="save-indicator" id="save-{{ $s->id }}">
                  <span class="lds-ripple"><div></div><div></div></span>
                  <span>Saving...</span>
                </span>
              </td>
              <td class="py-2 pr-3">
                @php $cnt = count($current); @endphp
                <span class="px-2 py-1 rounded-full text-xs {{ $cnt === 7 ? 'bg-emerald-100 text-emerald-800' : ($cnt > 7 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ $cnt }} Subjects</span>
              </td>
              <td class="py-2 pr-3">
                <a class="px-2 py-1 border rounded hover:bg-slate-50" href="{{ route('headmaster.students.show', $s->id) }}">View</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
function filterRows(){
  const q = (document.getElementById('search').value || '').trim().toUpperCase();
  document.querySelectorAll('#rows tr').forEach(tr => {
    const name = tr.getAttribute('data-name') || '';
    tr.style.display = !q || name.includes(q) ? '' : 'none';
  });
}

function saveSubjects(studentId, selected){
  const indicator = document.getElementById('save-'+studentId);
  indicator?.classList.add('show');
  return fetch(`{{ url('/headmaster/students') }}/${studentId}/subjects`, {
    method: 'PATCH',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    body: JSON.stringify({ subjects: selected })
  }).then(async r => {
    const j = await r.json().catch(()=>({}));
    if (!r.ok || j.ok === false) {
      console.warn('Save failed', j);
    }
    return j;
  }).finally(() => {
    setTimeout(()=> indicator?.classList.remove('show'), 600);
  });
}

// Attach handlers
 document.querySelectorAll('[data-student]').forEach(container => {
   const id = container.getAttribute('data-student');
   container.querySelectorAll('.subject-chip').forEach(chip => {
     chip.addEventListener('click', async () => {
       chip.classList.toggle('active');
       const selected = Array.from(container.querySelectorAll('.subject-chip.active')).map(el => el.getAttribute('data-code'));
       // Enforce exactly 7 in UI: block more than 7
       if (selected.length > 7) {
         chip.classList.remove('active');
         return; // do not save
       }
       // Only save when exactly 7
       if (selected.length === 7) {
         try { await saveSubjects(id, selected); } catch(_) { /* ignore */ }
       }
       // Update status badge count and color
       const statusTd = container.closest('tr').querySelector('td:nth-child(3) span');
       if (statusTd) {
         statusTd.textContent = `${selected.length} Subjects`;
         statusTd.className = 'px-2 py-1 rounded-full text-xs ' + (selected.length === 7 ? 'bg-emerald-100 text-emerald-800' : (selected.length > 7 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'));
       }
     });
   });
 });
</script>
@endsection
