@extends('layouts.headmaster')

@section('title','Overall Performance | Headmaster')

@section('content')
<div class="max-w-7xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Overall Performance</h1>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded ring-1 ring-slate-200 p-4">
      <div class="text-slate-600 mb-2">Performance Trend</div>
      <canvas id="perfTrend" height="140"></canvas>
    </div>
    <div class="bg-white rounded ring-1 ring-slate-200 p-4">
      <div class="text-slate-600 mb-2">Summary</div>
      <div class="text-slate-500">Stats coming soon.</div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', ()=>{
    const ctx = document.getElementById('perfTrend');
    if (!ctx) return;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['2019','2020','2021','2022','2023','2024'],
        datasets: [{
          label: 'Average Score',
          data: [45, 51, 49, 55, 58, 60],
          fill: false,
          borderColor: '#1EB53A',
          tension: 0.2
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  });
</script>
@endpush
