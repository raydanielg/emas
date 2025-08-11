@extends('layouts.headmaster')

@section('title','Overall Performance | Headmaster')

@section('content')
@php
  $schools = $schools ?? [];
  $selectedCode = $selectedCode ?? null;
  $grades = $grades ?? ['A'=>0,'B'=>0,'C'=>0,'D'=>0,'F'=>0];
  $avgPoints = $avgPoints ?? null;
  $gpa = $gpa ?? null;
  $total = $total ?? 0;
@endphp
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 grid place-items-center">
        <i class="fa-solid fa-clipboard-check"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Overall Performance</h1>
        <div class="text-slate-500 text-sm">Summary of grades and averages for your school</div>
      </div>
    </div>
  </div>

  @if(count($schools) > 1)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4 mb-5">
      <form method="get" class="flex items-center gap-3">
        <label class="text-sm text-slate-600">Select School</label>
        <select name="school" class="px-3 py-2 rounded-md ring-1 ring-slate-300 focus:ring-indigo-500 focus:outline-none">
          @foreach($schools as $s)
            <option value="{{ $s['code'] }}" @selected($selectedCode===$s['code'])>{{ $s['name'] }} ({{ $s['code'] }})</option>
          @endforeach
        </select>
        <button class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Apply</button>
      </form>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    @php $palette = ['A'=>'#10b981','B'=>'#3b82f6','C'=>'#f59e0b','D'=>'#ef4444','F'=>'#6b7280']; @endphp
    @foreach(['A','B','C','D','F'] as $g)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Grade {{ $g }}</div>
      <div class="text-3xl font-bold">{{ (int)($grades[$g] ?? 0) }}</div>
      <div class="mt-2 h-1.5 rounded" style="background-color: {{ $palette[$g] }}22">
        <div class="h-1.5 rounded" style="width: {{ $total? min(100, max(5, round(($grades[$g]??0)/max(1,$total)*100))) : 5 }}%; background-color: {{ $palette[$g] }}"></div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Total Candidates</div>
      <div class="text-3xl font-bold">{{ number_format((int)$total) }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Average Points</div>
      <div class="text-3xl font-bold">{{ $avgPoints !== null ? number_format($avgPoints,2) : '—' }}</div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">GPA</div>
      <div class="text-3xl font-bold">{{ $gpa !== null ? number_format($gpa,2) : '—' }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="font-semibold text-slate-700 mb-3">Grade Distribution</div>
      <canvas id="gradesPie" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="font-semibold text-slate-700 mb-3">Grade Counts</div>
      <canvas id="gradesBar" height="200"></canvas>
    </div>
  </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const gradeLabels = ['A','B','C','D','F'];
  const gradeValues = @json(array_values(array_map(fn($g)=> (int)$g, $grades)));
  const colors = ['#10b981','#3b82f6','#f59e0b','#ef4444','#6b7280'];

  const pieCtx = document.getElementById('gradesPie');
  if (pieCtx) {
    new Chart(pieCtx, {
      type: 'pie',
      data: { labels: gradeLabels, datasets: [{ data: gradeValues, backgroundColor: colors, borderColor: '#fff', borderWidth: 2 }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }

  const barCtx = document.getElementById('gradesBar');
  if (barCtx) {
    new Chart(barCtx, {
      type: 'bar',
      data: { labels: gradeLabels, datasets: [{ label: 'Count', data: gradeValues, backgroundColor: colors, borderRadius: 6 }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
    });
  }
</script>
