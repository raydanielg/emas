@extends('layouts.user')

@section('title', 'eMAS | User Dashboard')

@section('content')
  <!-- Top filter bar like PREMS: Region, District, School, Forms, refresh -->
  <div class="mb-4 flex flex-wrap items-center gap-3">
    <select id="regionSelect" class="border rounded-lg px-3 py-2 text-sm">
      <option>Manyara</option>
      <option>Dar es Salaam</option>
      <option>Mbeya</option>
      <option>Arusha</option>
    </select>
    <select id="districtSelect" class="border rounded-lg px-3 py-2 text-sm">
      <option>Babati (M)</option>
      <option>Ilala</option>
      <option>Kinondoni</option>
    </select>
    <select id="wardSelect" class="border rounded-lg px-3 py-2 text-sm min-w-[220px]">
      <option>All Wards</option>
      <option>Babati Ward</option>
      <option>Bagara Ward</option>
    </select>
    <select id="schoolSelect" class="border rounded-lg px-3 py-2 text-sm min-w-[280px]">
      <option value="">All Schools</option>
    </select>
    <select class="border rounded-lg px-3 py-2 text-sm">
      <option>All Forms</option>
      <option>Form I</option>
      <option>Form II</option>
      <option>Form III</option>
      <option>Form IV</option>
    </select>
    <button class="h-9 px-3 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Refresh</button>
    <div class="ml-auto text-xs text-slate-500">Last update: <span id="lastUpdate"></span></div>
  </div>

  <h2 class="text-xl font-semibold mb-3">User dashboard</h2>

  <!-- KPI tiles: user-centric -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="rounded-lg p-4 bg-emerald-700 text-white">
      <div class="text-3xl font-bold" id="kpiCentres">0</div>
      <div class="opacity-90">Assigned centres (schools)</div>
    </div>
    <div class="rounded-lg p-4 bg-sky-600 text-white">
      <div class="text-3xl font-bold" id="kpiStudents">0</div>
      <div class="opacity-90">Total students (across centres)</div>
    </div>
    <div class="rounded-lg p-4 bg-teal-600 text-white">
      <div class="text-3xl font-bold" id="kpiEntries">0</div>
      <div class="opacity-90">Entries entered by you</div>
    </div>
    <div class="rounded-lg p-4 bg-amber-500 text-white">
      <div class="text-3xl font-bold" id="kpiRemainder">0</div>
      <div class="opacity-90">Remainder (students pending)</div>
    </div>
  </div>

  <!-- Full-width Marks entry progress (district) -->
  <div class="rounded-lg bg-white ring-1 ring-gray-200/70 mb-4">
    <div class="px-4 py-2 border-b font-semibold flex items-center justify-between">
      <span>Marks entry progress (district)</span>
      <span class="text-xs text-slate-500">Filter: <span id="activeDistrict">Babati (M)</span></span>
    </div>
    <div class="p-4 overflow-auto">
      <table class="text-xs min-w-[1200px]">
        <thead>
          <tr class="text-left text-slate-500 align-bottom">
            <th class="py-2 pr-3 sticky left-0 bg-white">#</th>
            <th class="py-2 pr-3 sticky left-6 bg-white">Centre name</th>
            <th class="py-2 px-2 text-center">Female</th>
            <th class="py-2 px-2 text-center">Male</th>
            <th class="py-2 px-2 text-center">Total</th>
            <th class="py-2 px-2 text-center text-slate-600">Civics</th>
            <th class="py-2 px-2 text-center text-slate-600">History</th>
            <th class="py-2 px-2 text-center text-slate-600">Geography</th>
            <th class="py-2 px-2 text-center text-slate-600">Kiswahili</th>
            <th class="py-2 px-2 text-center text-slate-600">English</th>
            <th class="py-2 px-2 text-center text-slate-600">Physics</th>
            <th class="py-2 px-2 text-center text-slate-600">Chemistry</th>
            <th class="py-2 px-2 text-center text-slate-600">Biology</th>
            <th class="py-2 px-2 text-center text-slate-600">Basic Math</th>
          </tr>
        </thead>
        <tbody id="progressBody" class="divide-y"></tbody>
      </table>
    </div>
  </div>

  <!-- Main grid: Left (tables+chart), Right (status+progress) -->
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <!-- Left column -->
    <div class="xl:col-span-2 space-y-4">
      
      <!-- Pass rate chart (district) -->
      <div class="rounded-lg bg-white ring-1 ring-gray-200/70">
        <div class="px-4 py-2 border-b font-semibold">Pass rate (district)</div>
        <div class="p-4 h-72">
          <canvas id="passChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Right column -->
    <div class="space-y-4">
      <div class="rounded-lg bg-white ring-1 ring-gray-200/70">
        <div class="px-4 py-2 border-b font-semibold">Completed schools</div>
        <ul id="completedList" class="p-4 space-y-2 text-sm"></ul>
      </div>

      <div class="rounded-lg bg-white ring-1 ring-gray-200/70">
        <div class="px-4 py-2 border-b font-semibold">Recent activity</div>
        <ul id="activityList" class="p-4 space-y-2 text-sm"></ul>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Last update time
  const last = document.getElementById('lastUpdate');
  if (last) {
    const d = new Date();
    const pad = (n)=> String(n).padStart(2,'0');
    last.textContent = `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  }

  // Subjects fallback (used if API returns none)
  let subjects = ['Civics','History','Geography','Kiswahili','English','Physics','Chemistry','Biology','Basic Math'];

  // Rendering helpers
  let passChartRef = null;
  function renderMatrix(centres) {
    const tbody = document.getElementById('progressBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    centres.forEach((c, idx) => {
      const tr = document.createElement('tr');
      const total = (c.female||0) + (c.male||0);
      const col = (html, extra='') => { const td = document.createElement('td'); td.className = `py-1 px-2 ${extra}`; td.innerHTML = html; return td; };
      tr.appendChild(col(String(idx+1), 'sticky left-0 bg-white font-medium pr-3'));
      tr.appendChild(col(c.name, 'sticky left-6 bg-white pr-3 min-w-[260px]'));
      tr.appendChild(col(String(c.female), 'text-center'));
      tr.appendChild(col(String(c.male), 'text-center'));
      tr.appendChild(col(String(total), 'text-center'));
      subjects.forEach(s => {
        const v = c.progress[s] ?? 0;
        let bg = 'bg-rose-600';
        if (v >= 100) bg = 'bg-emerald-600';
        else if (v > 0) bg = 'bg-amber-500';
        const cell = document.createElement('td');
        cell.className = `text-center text-white text-[11px] font-semibold`;
        cell.innerHTML = `<span class="inline-flex items-center justify-center min-w-[34px] px-2 py-1 rounded ${bg}">${v}%</span>`;
        tr.appendChild(cell);
      });
      tbody.appendChild(tr);
    });
  }

  // Pass rate chart per centre (demo %)
  function renderPassChart(centres) {
    const ctx = document.getElementById('passChart');
    if (!ctx || !window.Chart) return;
    const labels = centres.map(c => c.name.split(' - ')[0]);
    const passData = centres.map(c => {
      const vals = subjects.map(s => (c.progress[s] ?? 0));
      const avg = Math.round(vals.reduce((a,b)=>a+b,0) / vals.length);
      return Math.min(100, Math.max(0, avg));
    });
    if (passChartRef) passChartRef.destroy();
    passChartRef = new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Pass %', data: passData, backgroundColor: '#0284c7', borderRadius: 6 }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, max: 100, ticks: { color: '#64748b' }, grid: { color: '#e2e8f0' } },
          x: { ticks: { color: '#64748b' }, grid: { display: false } }
        },
        plugins: { legend: { display: false } }
      }
    });
  }

  // Completed schools (all subjects 100)
  function renderCompleted(centres) {
    const completedList = document.getElementById('completedList');
    if (!completedList) return;
    const completed = centres.filter(c => subjects.every(s => (c.progress[s] ?? 0) >= 100));
    completedList.innerHTML = completed.length ? '' : '<li class="text-slate-500">No completed schools yet</li>';
    completed.forEach(c => {
      const li = document.createElement('li');
      li.className = 'flex items-center justify-between';
      li.innerHTML = `<span>${c.name}</span><span class="text-emerald-700 font-semibold">100%</span>`;
      completedList.appendChild(li);
    });
  }

  // Recent activity (demo)
  function renderActivity() {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    const items = [
      { text: 'Entered Biology scores for 32 students', mins: 5 },
      { text: 'Updated Kiswahili paper for SS3399', mins: 18 },
      { text: 'Marked Physics for Azimio Secondary', mins: 52 },
      { text: 'Logged in from desktop app', mins: 90 },
    ];
    activityList.innerHTML = '';
    items.forEach(i => {
      const li = document.createElement('li');
      li.className = 'flex items-center justify-between';
      li.innerHTML = `<span>${i.text}</span><span class="text-xs text-slate-500">${i.mins} mins ago</span>`;
      activityList.appendChild(li);
    });
  }

  // Initial render and district filter
  async function fetchAndRender() {
    const region = document.getElementById('regionSelect')?.value || '';
    const district = document.getElementById('districtSelect')?.value || '';
    const ward = document.getElementById('wardSelect')?.value || '';
    const school = document.getElementById('schoolSelect')?.value || '';
    document.getElementById('activeDistrict').textContent = district || 'All';

    const params = new URLSearchParams({ region, district, ward, school });
    const res = await fetch(`/api/dashboard/summary?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return; // optionally handle error UI
    const data = await res.json();

    // Update subjects if provided
    if (Array.isArray(data.subjects) && data.subjects.length) subjects = data.subjects;

    // KPIs
    document.getElementById('kpiCentres').textContent = (data.kpis?.assigned_centres ?? 0).toLocaleString();
    document.getElementById('kpiStudents').textContent = (data.kpis?.total_students ?? 0).toLocaleString();
    document.getElementById('kpiEntries').textContent = (data.kpis?.entries_by_you ?? 0).toLocaleString();
    document.getElementById('kpiRemainder').textContent = (data.kpis?.remainder ?? 0).toLocaleString();

    // Table + chart + completed
    const centres = (data.centres || []).map(c => ({ name: c.name, female: c.female, male: c.male, progress: c.progress || {} }));
    renderMatrix(centres);
    renderPassChart(centres);
    renderCompleted(centres);

    // Populate school select from centres
    const schoolSel = document.getElementById('schoolSelect');
    if (schoolSel) {
      const current = schoolSel.value;
      schoolSel.innerHTML = '<option value="">All Schools</option>' + centres.map((c, i)=>`<option value="${c.id||''}">${c.name}</option>`).join('');
      // keep selection if exists
      if ([...schoolSel.options].some(o=>o.value===current)) schoolSel.value = current;
    }

    // Recent activity (basic)
    renderActivity();
  }

  // Bind filters
  ['regionSelect','districtSelect','wardSelect','schoolSelect'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', fetchAndRender);
  });

  // Initial
  fetchAndRender();
</script>
@endpush
