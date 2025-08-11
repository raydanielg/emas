@extends('layouts.admin')

@section('title','Admin | Dashboard')
@section('page_title','Dashboard')

@section('content')
<!-- Top filter toolbar (green) -->
<div class="mb-4 p-2 rounded bg-emerald-600 text-white flex flex-wrap items-center gap-2">
  <button class="px-3 py-2 bg-emerald-700/50 rounded"><i class="fa-solid fa-bars"></i></button>
  <select class="bg-white/15 border border-white/20 rounded px-2 py-1">
    <option>Region</option>
  </select>
  <select class="bg-white/15 border border-white/20 rounded px-2 py-1">
    <option>District</option>
  </select>
  <select class="bg-white/15 border border-white/20 rounded px-2 py-1">
    <option>School</option>
  </select>
  <select class="bg-white/15 border border-white/20 rounded px-2 py-1">
    <option>All forms</option>
  </select>
  <button class="ml-auto px-3 py-1 rounded bg-white/10 hover:bg-white/20"><i class="fa-solid fa-rotate"></i></button>
  <div class="flex items-center gap-2">
    <div class="w-8 h-8 rounded-full bg-white/20 grid place-items-center"><i class="fa-solid fa-user"></i></div>
    <span class="text-sm">{{ Auth::user()->email ?? '' }}</span>
  </div>
  <span class="text-xs ml-4 opacity-80">Last update: {{ now()->format('d-m-Y H:i:s') }}</span>
  </div>

<!-- Summary cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
  <div class="p-4 rounded bg-cyan-600 text-white">
    <div class="text-sm opacity-90">Centres</div>
    <div class="text-3xl font-bold mt-1">{{ number_format($cards['centres'] ?? 0) }}</div>
    <div class="mt-2 text-xs opacity-90"><a href="{{ route('admin.institutions.index') }}" class="underline">More info</a></div>
  </div>
  <div class="p-4 rounded bg-emerald-600 text-white">
    <div class="text-sm opacity-90">Qualified students</div>
    <div class="text-3xl font-bold mt-1">{{ number_format($cards['qualified'] ?? 0) }}</div>
    <div class="mt-2 text-xs opacity-90"><a href="#" class="underline">More info</a></div>
  </div>
  <div class="p-4 rounded bg-rose-600 text-white">
    <div class="text-sm opacity-90">Disqualified students</div>
    <div class="text-3xl font-bold mt-1">{{ number_format($cards['disqualified'] ?? 0) }}</div>
    <div class="mt-2 text-xs opacity-90"><a href="#" class="underline">More info</a></div>
  </div>
  <div class="p-4 rounded bg-amber-500 text-white">
    <div class="text-sm opacity-90">Not admitted</div>
    <div class="text-3xl font-bold mt-1">{{ number_format($cards['not_admitted'] ?? 0) }}</div>
    <div class="mt-2 text-xs opacity-90"><a href="#" class="underline">More info</a></div>
  </div>
  </div>

<!-- Main content: left tables/charts + right status -->
<div class="mt-4 grid grid-cols-1 xl:grid-cols-4 gap-4">
  <!-- Left (xl: span 3) -->
  <div class="xl:col-span-3 space-y-4">
    <div class="bg-white p-4 rounded ring-1 ring-slate-200">
      <div class="font-semibold text-slate-800 mb-3">Candidate registration</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 text-slate-600">
              <th class="text-left p-2">Form</th>
              <th class="text-left p-2">Students</th>
              <th class="text-left p-2">Candidates</th>
              <th class="text-left p-2">Not admitted</th>
              <th class="text-left p-2">No exam number</th>
              <th class="text-left p-2">Without photo</th>
              <th class="text-left p-2">Progress</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($formsTable ?? []) as $r)
              <tr class="border-t border-slate-100">
                <td class="p-2">Form {{ $r['form'] }}</td>
                <td class="p-2">{{ $r['students'] }}</td>
                <td class="p-2">{{ $r['candidates'] }}</td>
                <td class="p-2">{{ $r['not_admitted'] }}</td>
                <td class="p-2">{{ $r['no_exam'] }}</td>
                <td class="p-2">{{ $r['no_photo'] }}</td>
                <td class="p-2">
                  <div class="w-full bg-slate-100 rounded h-2 overflow-hidden">
                    <div class="bg-emerald-500 h-2" style="width: {{ $r['progress'] }}%"></div>
                  </div>
                  <div class="text-xs text-slate-600 mt-1">{{ number_format($r['progress'],1) }}%</div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white p-4 rounded ring-1 ring-slate-200">
      <div class="font-semibold text-slate-800 mb-2">Registration by sex</div>
      <canvas id="chartSexByForm" height="140"></canvas>
    </div>
  </div>

  <!-- Right column -->
  <div class="space-y-4">
    <div class="bg-emerald-600 text-white rounded p-4">
      <div class="flex items-center gap-2"><i class="fa-regular fa-clipboard"></i><span>Completed CA</span></div>
      <div class="mt-2 flex items-center gap-2"><i class="fa-regular fa-circle-check"></i><span>0</span></div>
      <div class="mt-2 flex items-center gap-2"><i class="fa-regular fa-circle"></i><span>Out of CA</span></div>
    </div>
    <div class="bg-white rounded p-4 ring-1 ring-slate-200">
      <div class="font-semibold text-slate-800 mb-2">Students registration progress</div>
      <div class="w-full bg-slate-100 rounded h-3 overflow-hidden">
        @php $total = ($cards['qualified']??0)+($cards['not_admitted']??0); $pct=$total? round((($cards['qualified']??0)/$total)*100):0; @endphp
        <div class="bg-emerald-500 h-3" style="width: {{ $pct }}%"></div>
      </div>
      <div class="mt-1 text-xs text-slate-600">{{ $cards['qualified'] ?? 0 }}/{{ $total }} ({{ $pct }}%)</div>
    </div>
  </div>
</div>

<!-- District chart -->
<div class="mt-6 bg-white p-4 rounded ring-1 ring-slate-200">
  <div class="font-semibold text-slate-800 mb-2">Schools by District</div>
  <canvas id="chartSchoolsByDistrict" height="140"></canvas>
  </div>

<script>
  // Registration by sex chart
  const sexByForm = @json($sexByForm ?? []);
  if (document.getElementById('chartSexByForm')) {
    const ctxS = document.getElementById('chartSexByForm');
    const labelsS = sexByForm.map(i=>`Form ${i.form}`);
    const male = sexByForm.map(i=>i.male);
    const female = sexByForm.map(i=>i.female);
    new Chart(ctxS, {
      type: 'bar',
      data: { labels: labelsS, datasets: [
        { label: 'Male', data: male, backgroundColor: 'hsl(160 70% 40%)', borderRadius: 4 },
        { label: 'Female', data: female, backgroundColor: 'hsl(220 70% 60%)', borderRadius: 4 },
      ]},
      options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision:0 } } } }
    });
  }

  // District chart
  const districts = @json(($byDistrict ?? collect())->pluck('district'));
  const schools = @json(($byDistrict ?? collect())->pluck('schools'));
  if (document.getElementById('chartSchoolsByDistrict')) {
    const ctx = document.getElementById('chartSchoolsByDistrict');
    const colors = schools.map((_,i)=>`hsl(${(i*37)%360} 70% 55%)`);
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: districts,
        datasets: [{ label: 'Schools', data: schools, backgroundColor: colors, borderRadius: 6 }]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision:0 } } }, plugins: { legend: { display: false } } }
    });
  }
</script>
@endsection
