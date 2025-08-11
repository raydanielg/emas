@extends('layouts.headmaster')

@section('title','School Reports | Headmaster')

@section('content')
@php
  $stats = $stats ?? [
    'schools' => [],
    'students_total' => 0,
    'teachers_total' => 0,
    'subjects_total' => 0,
    'gender' => ['male'=>0,'female'=>0],
    'by_form' => [],
  ];
  $schools = $stats['schools'] ?? [];
  $gender = $stats['gender'] ?? ['male'=>0,'female'=>0];
  $byForm = $stats['by_form'] ?? [];
@endphp
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 grid place-items-center">
        <i class="fa-solid fa-chart-pie"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">School Reports</h1>
        <div class="text-slate-500 text-sm">Scoped to your assigned school(s)</div>
      </div>
    </div>
  </div>

  @if(!empty($schools))
  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4 mb-5">
    <div class="text-slate-600 text-sm mb-1">Schools</div>
    <div class="flex flex-wrap gap-2">
      @foreach($schools as $s)
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-700"><i class="fa-solid fa-school mr-1"></i>{{ $s }}</span>
      @endforeach
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Students</div>
      <div class="text-3xl font-bold">{{ number_format((int)$stats['students_total']) }}</div>
      <div class="mt-2 h-1.5 rounded bg-emerald-100">
        <div class="h-1.5 rounded bg-emerald-500" style="width: {{ (int)max(5, min(100, $stats['students_total'] ? 100 : 5)) }}%"></div>
      </div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Teachers</div>
      <div class="text-3xl font-bold">{{ number_format((int)$stats['teachers_total']) }}</div>
      <div class="mt-2 h-1.5 rounded bg-sky-100">
        <div class="h-1.5 rounded bg-sky-500" style="width: {{ (int)max(5, min(100, $stats['teachers_total'] ? 100 : 5)) }}%"></div>
      </div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="text-slate-500 text-xs">Subjects</div>
      <div class="text-3xl font-bold">{{ number_format((int)$stats['subjects_total']) }}</div>
      <div class="mt-2 h-1.5 rounded bg-amber-100">
        <div class="h-1.5 rounded bg-amber-500" style="width: {{ (int)max(5, min(100, $stats['subjects_total'] ? 100 : 5)) }}%"></div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold text-slate-700">Gender Breakdown</div>
      </div>
      <canvas id="genderChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold text-slate-700">Students by Class/Form</div>
      </div>
      <canvas id="formChart" height="200"></canvas>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const genderData = @json([ 'Male' => (int)($gender['male'] ?? 0), 'Female' => (int)($gender['female'] ?? 0) ]);
  const formData = @json($byForm);

  // Gender Pie
  const gctx = document.getElementById('genderChart');
  if (gctx) {
    new Chart(gctx, {
      type: 'pie',
      data: {
        labels: Object.keys(genderData),
        datasets: [{
          data: Object.values(genderData),
          backgroundColor: ['#10b981','#f59e0b'],
          borderColor: '#ffffff',
          borderWidth: 2,
        }]
      },
      options: {
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }

  // Form Bar
  const fctx = document.getElementById('formChart');
  if (fctx) {
    const labels = Object.keys(formData);
    const values = Object.values(formData);
    new Chart(fctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Students',
          data: values,
          backgroundColor: '#3b82f6',
          hoverBackgroundColor: '#2563eb',
          borderRadius: 6,
        }]
      },
      options: {
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { display: false } }
      }
    });
  }
</script>
@endsection
