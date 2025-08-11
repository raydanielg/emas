@extends('layouts.user')
@section('title','Marking | eMAS')
@section('content')
<div class="max-w-6xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Marking</h1>
    <p class="text-slate-600 text-sm">Manage marking workflows: Students and Centres.</p>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <a href="{{ route('marking.students') }}" class="block rounded-xl p-5 bg-gradient-to-br from-emerald-50 to-white ring-1 ring-emerald-100 hover:shadow">
      <div class="flex items-center gap-3 text-emerald-700">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"><i class="fa-solid fa-users"></i></span>
        <div>
          <div class="font-semibold text-emerald-900">Students</div>
          <div class="text-sm text-emerald-700/80">Browse and manage students.</div>
        </div>
      </div>
    </a>
    <a href="{{ route('marking.centres') }}" class="block rounded-xl p-5 bg-gradient-to-br from-sky-50 to-white ring-1 ring-sky-100 hover:shadow">
      <div class="flex items-center gap-3 text-sky-700">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100"><i class="fa-solid fa-school"></i></span>
        <div>
          <div class="font-semibold text-sky-900">Centres</div>
          <div class="text-sm text-sky-700/80">View assigned centres.</div>
        </div>
      </div>
    </a>
  </div>

  <!-- Live recent data -->
  <div class="mt-8">
    <div class="mb-3 flex items-center justify-between">
      <h2 class="text-lg font-semibold">Recent Activity</h2>
      <div class="flex items-center gap-3">
        <button id="autoRefreshToggle" class="text-sm px-2.5 py-1 rounded-md ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100" aria-pressed="true">Auto: On</button>
        <button id="refreshRecent" class="text-sm text-emas-green hover:text-emas-greenDark">Refresh</button>
      </div>
    </div>

    <!-- Summary cards -->
    <div id="recentSummary" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Today card -->
      <div class="rounded-xl p-4 bg-gradient-to-br from-emerald-50 to-white ring-1 ring-emerald-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-emerald-700">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100"><i class="fa-solid fa-calendar-day"></i></span>
            <span class="text-sm font-medium">Today Entered</span>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <div class="text-3xl font-extrabold text-emerald-900" data-key="today">—</div>
          <span class="text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700" data-delta="today" hidden>
            <i class="fa-solid fa-arrow-trend-up"></i> <span>+0</span>
          </span>
          <div class="text-emerald-600" data-loader>
            <div class="lds-ripple lds-sm"><div></div><div></div></div>
          </div>
        </div>
      </div>

      <!-- Last hour card -->
      <div class="rounded-xl p-4 bg-gradient-to-br from-indigo-50 to-white ring-1 ring-indigo-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-indigo-700">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100"><i class="fa-solid fa-clock"></i></span>
            <span class="text-sm font-medium">Last Hour</span>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <div class="text-3xl font-extrabold text-indigo-900" data-key="last_hour">—</div>
          <span class="text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700" data-delta="last_hour" hidden>
            <i class="fa-solid fa-arrow-trend-up"></i> <span>+0</span>
          </span>
          <div class="text-indigo-600" data-loader>
            <div class="lds-ripple lds-sm"><div></div><div></div></div>
          </div>
        </div>
      </div>

      <!-- My Today card -->
      <div class="rounded-xl p-4 bg-gradient-to-br from-amber-50 to-white ring-1 ring-amber-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-amber-700">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-100"><i class="fa-solid fa-user-check"></i></span>
            <span class="text-sm font-medium">My Entries Today</span>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <div class="text-3xl font-extrabold text-amber-900" data-key="mine_today">—</div>
          <span class="text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700" data-delta="mine_today" hidden>
            <i class="fa-solid fa-arrow-trend-up"></i> <span>+0</span>
          </span>
          <div class="text-amber-600" data-loader>
            <div class="lds-ripple lds-sm"><div></div><div></div></div>
          </div>
        </div>
      </div>

      <!-- My Last hour card -->
      <div class="rounded-xl p-4 bg-gradient-to-br from-rose-50 to-white ring-1 ring-rose-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-rose-700">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100"><i class="fa-solid fa-user-clock"></i></span>
            <span class="text-sm font-medium">My Last Hour</span>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <div class="text-3xl font-extrabold text-rose-900" data-key="mine_last_hour">—</div>
          <span class="text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-700" data-delta="mine_last_hour" hidden>
            <i class="fa-solid fa-arrow-trend-up"></i> <span>+0</span>
          </span>
          <div class="text-rose-600" data-loader>
            <div class="lds-ripple lds-sm"><div></div><div></div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent list -->
    <div class="mt-4 rounded-xl ring-1 ring-gray-200 bg-white">
      <div class="px-4 py-3 border-b text-sm font-semibold text-slate-700">Latest 10 marks</div>
      <div id="recentList" class="divide-y">
        <div class="flex items-center justify-center py-10 text-emas-green">
          <div class="lds-ripple lds-xs"><div></div><div></div></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
  const summaryEl = document.getElementById('recentSummary');
  const listEl = document.getElementById('recentList');
  const btn = document.getElementById('refreshRecent');
  const autoBtn = document.getElementById('autoRefreshToggle');
  let autoOn = true; let timer = null; let prev = null;

  function renderSummary(data){
    summaryEl.querySelector('[data-key="today"]').textContent = (data.today ?? 0).toLocaleString();
    summaryEl.querySelector('[data-key="last_hour"]').textContent = (data.last_hour ?? 0).toLocaleString();
    if (summaryEl.querySelector('[data-key=\"mine_today\"]')) {
      summaryEl.querySelector('[data-key="mine_today"]').textContent = (data.mine_today ?? 0).toLocaleString();
    }
    if (summaryEl.querySelector('[data-key=\"mine_last_hour\"]')) {
      summaryEl.querySelector('[data-key="mine_last_hour"]').textContent = (data.mine_last_hour ?? 0).toLocaleString();
    }
    // hide small loaders
    summaryEl.querySelectorAll('[data-loader]').forEach(el=> el.remove());

    // deltas
    if (prev){
      const keys = ['today','last_hour','mine_today','mine_last_hour'];
      keys.forEach(k=>{
        const el = summaryEl.querySelector(`[data-delta="${k}"]`);
        if (!el) return;
        const curr = data[k] ?? 0; const old = prev[k] ?? 0; const d = curr - old;
        const val = el.querySelector('span:last-child');
        if (d > 0){
          el.hidden = false; val.textContent = `+${d.toLocaleString()}`;
          // set colors by card theme
          if (k.includes('mine_')) { el.className = 'text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700'; }
          else if (k === 'last_hour') { el.className = 'text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700'; }
          else { el.className = 'text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700'; }
          const icon = el.querySelector('i'); if (icon){ icon.className = 'fa-solid fa-arrow-trend-up'; }
        } else if (d < 0){
          el.hidden = false; val.textContent = `${d.toLocaleString()}`;
          // negative unlikely, use neutral slate
          el.className = 'text-xs font-medium inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-700';
          const icon = el.querySelector('i'); if (icon){ icon.className = 'fa-solid fa-arrow-trend-down'; }
        } else {
          // zero change => hide
          el.hidden = true;
        }
      });
    }
    prev = { ...data };
  }

  function renderList(items){
    listEl.innerHTML = '';
    if (!items || items.length === 0){
      const d = document.createElement('div');
      d.className = 'px-4 py-8 text-slate-500 text-sm';
      d.textContent = 'No recent entries.';
      listEl.appendChild(d); return;
    }
    items.forEach(it => {
      const row = document.createElement('div');
      row.className = 'px-4 py-3 flex items-center justify-between hover:bg-slate-50';
      const left = document.createElement('div');
      left.className = 'flex items-center gap-3';
      const badge = document.createElement('span');
      badge.className = 'inline-flex items-center justify-center h-8 w-8 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold';
      badge.textContent = (it.score ?? '—');
      const meta = document.createElement('div');
      meta.className = 'text-sm';
      meta.innerHTML = `<div class="font-medium">${it.subject} • ${it.school}</div>
                        <div class="text-slate-500">${it.exam_number} — ${it.last_name}, ${it.first_name} • Form ${it.form} • ${new Date(it.updated_at).toLocaleString()}</div>`;
      left.appendChild(badge); left.appendChild(meta);
      row.appendChild(left);
      listEl.appendChild(row);
    });
  }

  async function loadRecent(){
    // show mini ripple in summary cards by toggling visibility
    try{
      const res = await fetch('{{ url('/marking/api/recent') }}', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const j = await res.json();
      if (!j.ok) throw new Error('Failed');
      renderSummary(j.summary||{});
      renderList(j.recent||[]);
    }catch(e){
      renderList([]);
    }
  }

  btn && btn.addEventListener('click', (e)=>{ e.preventDefault(); loadRecent(); });
  function startAuto(){ if (timer) clearInterval(timer); timer = setInterval(loadRecent, 30000); }
  function stopAuto(){ if (timer) { clearInterval(timer); timer = null; } }
  autoBtn && autoBtn.addEventListener('click', (e)=>{
    e.preventDefault(); autoOn = !autoOn; autoBtn.setAttribute('aria-pressed', String(autoOn));
    autoBtn.textContent = autoOn ? 'Auto: On' : 'Auto: Off';
    if (autoOn) startAuto(); else stopAuto();
  });
  loadRecent(); startAuto();

})();
</script>
@endpush
