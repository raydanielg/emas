@extends('layouts.headmaster')

@section('title','Results Reports | Headmaster')

@section('content')
@php
  $reports = $reports ?? collect();
  $schools = $schools ?? [];
@endphp
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 grid place-items-center">
        <i class="fa-solid fa-file-pdf"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Results Reports</h1>
        <div class="text-slate-500 text-sm">Uploaded by Admin, filtered by your school(s)</div>
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

  <div class="bg-white rounded-xl ring-1 ring-slate-200 overflow-hidden">
    <div class="p-4 border-b text-slate-700 font-medium flex items-center gap-2">
      <i class="fa-solid fa-table"></i>
      <span>Available Result Reports</span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Title</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Exam</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Term</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Year</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Status</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Posted</th>
            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($reports as $r)
          <tr>
            <td class="px-4 py-3">
              <div class="font-medium text-slate-800">{{ $r->title ?? 'Result Report' }}</div>
              @if(!empty($r->school_code))
              <div class="text-xs text-slate-500">School Code: {{ $r->school_code }}</div>
              @endif
            </td>
            <td class="px-4 py-3 text-slate-700">{{ $r->exam ?? '—' }}</td>
            <td class="px-4 py-3 text-slate-700">{{ $r->term ?? '—' }}</td>
            <td class="px-4 py-3 text-slate-700">{{ $r->year ?? '—' }}</td>
            <td class="px-4 py-3">
              @php $status = strtolower((string)($r->status ?? 'ready')); @endphp
              @if($status === 'pending')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-700"><i class="fa-solid fa-clock mr-1"></i>Pending</span>
              @elseif($status === 'ready' || $status === 'published')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700"><i class="fa-solid fa-check mr-1"></i>Ready</span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">{{ ucfirst($status) }}</span>
              @endif
            </td>
            <td class="px-4 py-3 text-slate-700">{{ isset($r->created_at) ? \Carbon\Carbon::parse($r->created_at)->diffForHumans() : '—' }}</td>
            <td class="px-4 py-3 text-right">
              @if(!empty($r->download_url))
              <button data-url="{{ $r->download_url }}" class="download-btn inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none disabled:opacity-60">
                <span class="btn-label"><i class="fa-solid fa-download"></i> Download PDF</span>
                <span class="btn-loading hidden">
                  <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                </span>
              </button>
              @else
              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-500"><i class="fa-solid fa-ban mr-1"></i> No file</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
              <div class="flex flex-col items-center gap-2">
                <i class="fa-regular fa-folder-open text-2xl text-slate-300"></i>
                <div>No result reports available yet.</div>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const btns = document.querySelectorAll('.download-btn');
  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      const url = btn.getAttribute('data-url');
      if (!url) return;
      const label = btn.querySelector('.btn-label');
      const loading = btn.querySelector('.btn-loading');
      btn.disabled = true; label.classList.add('hidden'); loading.classList.remove('hidden');
      // Trigger browser download
      window.location.href = url;
      // Re-enable after a short delay (browser doesn't expose download finished event)
      setTimeout(() => { btn.disabled = false; loading.classList.add('hidden'); label.classList.remove('hidden'); }, 2500);
    });
  });
</script>
@endsection
