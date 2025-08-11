@extends('layouts.user')
@section('title', ($school->name ?? 'Centre').' | Sheet | Marking | eMAS')
@section('content')
<?php
  $roman = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI'];
?>
<div class="max-w-[100vw]">
  <div class="max-w-7xl mx-auto mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">{{ $school->name }}</h1>
      <div class="text-slate-600 text-sm">Code {{ $school->code ?? '' }}</div>
    </div>
    <div class="flex items-center gap-2">
      @if(!empty($active_subject_name))
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">
          <i class="fa fa-edit"></i>
          Editing subject: <strong>{{ $active_subject_name }}</strong>
        </span>
      @else
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 text-sm">
          <i class="fa fa-lock"></i>
          No subject assigned
        </span>
      @endif
      <a href="{{ route('marking.centres') }}" class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm show-loader">Back to Centres</a>
    </div>
  </div>

  <div id="saveBanner" class="sticky top-0 z-40 hidden">
    <div class="mx-auto max-w-7xl">
      <div class="rounded-lg bg-amber-50 text-amber-900 border border-amber-200 px-4 py-2 text-sm flex items-center gap-2" data-state="idle">
        <span class="state idle hidden">Ready</span>
        <span class="state saving flex items-center gap-2 hidden">
          <span class="inline-block w-4 h-4 border-2 border-amber-400 border-r-transparent rounded-full animate-spin"></span>
          Saving…
        </span>
        <span class="state saved hidden text-emerald-700">Saved</span>
        <span class="state error hidden text-rose-700">Failed — check network</span>
      </div>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-[13px] bg-white rounded-xl ring-1 ring-gray-200">
      <thead class="bg-slate-50 text-slate-700">
        <tr>
          <th class="px-3 py-2 text-left font-semibold">CAND NO</th>
          <th class="px-3 py-2 text-left font-semibold">CANDIDATE FULL NAME</th>
          <th class="px-3 py-2 text-left font-semibold">SEX</th>
          <th class="px-3 py-2 text-left font-semibold">FORM</th>
          @foreach($subjects as $sub)
            <th class="px-2 py-2 text-center font-semibold whitespace-nowrap {{ ($active_subject_id && $active_subject_id !== $sub->id) ? 'text-slate-400' : '' }}" data-subject-header="{{ $sub->id }}">{{ $sub->name }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($students as $st)
        <tr class="border-t">
          <td class="px-3 py-1 whitespace-nowrap">{{ $st->exam_number }}</td>
          <td class="px-3 py-1 whitespace-nowrap">{{ $st->last_name }}, {{ $st->first_name }}</td>
          <td class="px-3 py-1 text-center">{{ $st->sex }}</td>
          <td class="px-3 py-1 text-center">{{ $roman[$st->form] ?? $st->form }}</td>
          @foreach($subjects as $sub)
            <?php $val = $marks[$st->id][$sub->id] ?? ''; $editable = in_array($sub->id, $editable_subject_ids, true); ?>
            <td class="px-1 py-1 text-center align-middle {{ !$editable ? 'opacity-60' : '' }}" data-subject-col="{{ $sub->id }}">
              @if($editable)
                <input 
                  type="text"
                  inputmode="decimal"
                  value="{{ $val }}"
                  class="w-20 px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 text-center bg-white"
                  data-student="{{ $st->id }}"
                  data-subject="{{ $sub->id }}"
                  data-form="{{ $st->form }}"
                  data-school="{{ $school->id }}"
                />
              @else
                <span class="inline-block w-20 px-2 py-1 rounded bg-slate-50 text-slate-500 border border-transparent cursor-not-allowed select-none">{{ $val }}</span>
              @endif
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const token = document.querySelector('meta[name="csrf-token"]').content;
  const banner = document.getElementById('saveBanner');
  const stateEls = {
    idle: banner.querySelector('.state.idle'),
    saving: banner.querySelector('.state.saving'),
    saved: banner.querySelector('.state.saved'),
    error: banner.querySelector('.state.error'),
  };
  let hideTimer = null;
  function setBanner(state){
    banner.classList.remove('hidden');
    Object.values(stateEls).forEach(el=>el.classList.add('hidden'));
    (stateEls[state]||stateEls.idle).classList.remove('hidden');
    if (hideTimer) clearTimeout(hideTimer);
    if (state==='saved') hideTimer = setTimeout(()=>banner.classList.add('hidden'), 1200);
  }

  // Row focus highlight and dim others
  const table = document.querySelector('table');
  function clearRowFocus(){
    document.querySelectorAll('tbody tr').forEach(tr=>{
      tr.classList.remove('bg-emerald-50');
      tr.classList.remove('opacity-50');
    });
  }
  table.addEventListener('focusin', (e)=>{
    const input = e.target.closest('input[data-student][data-subject]');
    if (!input) return;
    const row = input.closest('tr');
    document.querySelectorAll('tbody tr').forEach(tr=>{
      tr.classList.add('opacity-50');
    });
    if (row){
      row.classList.remove('opacity-50');
      row.classList.add('bg-emerald-50');
    }
  });
  table.addEventListener('focusout', (e)=>{
    // if focus left the table inputs, clear
    setTimeout(()=>{
      if (!table.contains(document.activeElement)){
        clearRowFocus();
      }
    }, 0);
  });

  async function saveMark(payload){
    setBanner('saving');
    try{
      const res = await fetch('{{ route('marking.api.marks.upsert') }}', {
        method:'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify(payload)
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      const j = await res.json();
      if (!j.ok) throw new Error(j.error||'Failed');
      setBanner('saved');
      return true;
    }catch(err){ console.error(err); setBanner('error'); return false; }
  }

  function normalizeScore(v){ if(v===''||v==null) return ''; const n = Number(v); if(Number.isNaN(n)) return null; if(n<0||n>100) return null; return String(n); }

  document.addEventListener('keydown', (e)=>{
    const t = e.target;
    if (t && t.matches('input[data-student][data-subject]') && (e.key==='Enter' || e.key==='Tab')){
      e.preventDefault();
      const val = normalizeScore(t.value.trim());
      if (val===null){ t.classList.add('ring-2','ring-rose-300'); return; } else { t.classList.remove('ring-2','ring-rose-300'); }
      const payload = {
        school_id: Number(t.dataset.school),
        student_id: Number(t.dataset.student),
        subject_id: Number(t.dataset.subject),
        form: Number(t.dataset.form),
        score: (val==='')? null : Number(val),
      };
      saveMark(payload).then(()=>{
        // Move focus right
        const cells = Array.from(document.querySelectorAll('input[data-student][data-subject]'));
        const idx = cells.indexOf(t);
        const next = cells[idx+1];
        if (next) next.focus();
      });
    }
  });
})();
</script>
@endpush
@endsection
