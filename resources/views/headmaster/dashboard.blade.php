@extends('layouts.headmaster')

@section('title','Headmaster Dashboard | eMAS')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Headmaster Panel</h1>
      <p class="text-slate-500 text-sm">Overview and insights for your school(s)</p>
    </div>
    <div class="flex items-center gap-3">
      <form method="get" action="{{ route('headmaster.dashboard') }}" class="hidden md:flex items-center gap-2">
        @if(!empty($schools))
          <select name="school" class="px-3 py-2 rounded border">
            <option value="">All Schools</option>
            @foreach($schools as $sch)
              <option value="{{ $sch }}" @selected(($activeSchool ?? '') === $sch)>{{ $sch }}</option>
            @endforeach
          </select>
          <button class="px-3 py-2 rounded bg-slate-800 text-white">Filter</button>
        @endif
      </form>
      <a href="{{ route('headmaster.upload') }}" class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark"><i class="fa-solid fa-cloud-arrow-up mr-2"></i>Upload Students</a>
    </div>
  </div>

  @if(session('status'))
    <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700">{{ session('status') }}</div>
  @endif

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-school"></i></div>
      <div>
        <div class="text-xs text-slate-500">Total Schools</div>
        <div class="text-xl font-semibold num-loading" data-loading="false">
          <span class="number">{{ $totalSchools }}</span>
          <span class="spinner"><span class="sr-only">Loading…</span><span class="lds-ring lds-sm"><div></div><div></div><div></div><div></div></span></span>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
      <div>
        <div class="text-xs text-slate-500">Total Students</div>
        <div class="text-xl font-semibold num-loading" data-loading="false">
          <span class="number">{{ $totalStudents }}</span>
          <span class="spinner"><span class="sr-only">Loading…</span><span class="lds-ring lds-sm"><div></div><div></div><div></div><div></div></span></span>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-chalkboard-user"></i></div>
      <div>
        <div class="text-xs text-slate-500">Total Teachers</div>
        <div class="text-xl font-semibold num-loading" data-loading="false">
          <span class="number">{{ $totalTeachers }}</span>
          <span class="spinner"><span class="sr-only">Loading…</span><span class="lds-ring lds-sm"><div></div><div></div><div></div><div></div></span></span>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-chart-line"></i></div>
      <div>
        <div class="text-xs text-slate-500">Active School</div>
        <div class="text-sm font-semibold">{{ $activeSchool ? $activeSchool : 'All' }}</div>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
      <h3 class="font-semibold mb-3">Students by Form</h3>
      <canvas id="barForms" height="180"></canvas>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
      <h3 class="font-semibold mb-3">Gender Distribution</h3>
      <canvas id="pieGender" height="180"></canvas>
    </div>
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
      <h3 class="font-semibold mb-3">Forms Table</h3>
      <ul class="space-y-2">
        @forelse($forms as $f)
          <li class="flex items-center justify-between">
            <span>{{ $f->form_level ?? 'Unknown' }}</span>
            <span class="text-slate-600">{{ $f->total }}</span>
          </li>
        @empty
          <li class="text-slate-500 text-sm">No data</li>
        @endforelse
      </ul>
    </div>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Recent Activity</h3>
      @if(!empty($schools))
        <form method="get" action="{{ route('headmaster.dashboard') }}" class="md:hidden">
          <select name="school" class="px-3 py-2 rounded border" onchange="this.form.submit()">
            <option value="">All Schools</option>
            @foreach($schools as $sch)
              <option value="{{ $sch }}" @selected(($activeSchool ?? '') === $sch)>{{ $sch }}</option>
            @endforeach
          </select>
        </form>
      @endif
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-500">
            <th class="py-2 pr-4">Name</th>
            <th class="py-2 pr-4">Form</th>
            <th class="py-2 pr-4">Admission</th>
            <th class="py-2 pr-4">Stream</th>
            <th class="py-2 pr-4">Added</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recent as $s)
            <tr class="border-t">
              <td class="py-2 pr-4">{{ $s->full_name }}</td>
              <td class="py-2 pr-4">{{ $s->form_level }}</td>
              <td class="py-2 pr-4">{{ $s->admission_no }}</td>
              <td class="py-2 pr-4">{{ $s->stream }}</td>
              <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($s->created_at)->diffForHumans() }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-slate-500">No activity yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const formsData = @json($forms);
  const genderData = @json($gender);

  // Bar chart: forms
  const labels = formsData.map(f => f.form_level || 'Unknown');
  const counts = formsData.map(f => Number(f.total));
  const ctxBar = document.getElementById('barForms');
  if (ctxBar) {
    new Chart(ctxBar, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Students', data: counts, backgroundColor: '#10b981' }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
  }

  // Pie chart: gender
  const gLabels = (genderData || []).map(g => g.gender || 'Unknown');
  const gCounts = (genderData || []).map(g => Number(g.total));
  const ctxPie = document.getElementById('pieGender');
  if (ctxPie && gLabels.length) {
    new Chart(ctxPie, {
      type: 'pie',
      data: { labels: gLabels, datasets: [{ data: gCounts, backgroundColor: ['#60a5fa','#f472b6','#fbbf24','#34d399'] }] },
    });
  }
</script>
@endpush
@endsection
