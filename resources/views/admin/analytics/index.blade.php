@extends('layouts.admin')

@section('title','Admin | Analytics')
@section('page_title','Live Analytics')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="bg-white p-4 rounded ring-1 ring-slate-200 lg:col-span-1">
    <div class="font-semibold text-slate-800 mb-2">Users by Role</div>
    <canvas id="chartUsersByRole" height="220"></canvas>
  </div>
  <div class="bg-white p-4 rounded ring-1 ring-slate-200 lg:col-span-2">
    <div class="font-semibold text-slate-800 mb-2">Schools by District</div>
    <canvas id="chartSchoolsByDistrict" height="140"></canvas>
  </div>
</div>

<div class="mt-6 bg-white p-4 rounded ring-1 ring-slate-200">
  <div class="font-semibold text-slate-800 mb-2">Students by District</div>
  <canvas id="chartStudentsByDistrict" height="120"></canvas>
</div>

<script>
  // Prepare data
  const usersByRole = @json($charts['usersByRole'] ?? []);
  const schoolsByDistrict = @json($charts['schoolsByDistrict'] ?? []);
  const studentsByDistrict = @json($charts['studentsByDistrict'] ?? []);

  // Users by Role - Pie
  (function(){
    const el = document.getElementById('chartUsersByRole'); if (!el) return;
    const labels = usersByRole.map(i=>i.label);
    const data = usersByRole.map(i=>i.value);
    const colors = data.map((_,i)=>`hsl(${(i*67)%360} 75% 55%)`);
    new Chart(el, {
      type: 'pie',
      data: { labels, datasets: [{ data, backgroundColor: colors }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  })();

  // Schools by District - Bar
  ;(function(){
    const el = document.getElementById('chartSchoolsByDistrict'); if (!el) return;
    const labels = schoolsByDistrict.map(i=>i.label);
    const data = schoolsByDistrict.map(i=>i.value);
    const colors = data.map((_,i)=>`hsl(${(i*37)%360} 70% 55%)`);
    new Chart(el, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Schools', data, backgroundColor: colors, borderRadius: 6 }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision:0 } } }, plugins: { legend: { display: false } } }
    });
  })();

  // Students by District - Bar
  ;(function(){
    const el = document.getElementById('chartStudentsByDistrict'); if (!el) return;
    const labels = studentsByDistrict.map(i=>i.label);
    const data = studentsByDistrict.map(i=>i.value);
    const colors = data.map((_,i)=>`hsl(${(i*19)%360} 70% 55%)`);
    new Chart(el, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Students', data, backgroundColor: colors, borderRadius: 6 }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision:0 } } }, plugins: { legend: { display: false } } }
    });
  })();
</script>
@endsection
