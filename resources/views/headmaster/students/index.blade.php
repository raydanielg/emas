@extends('layouts.headmaster')

@section('title','Manage Students | Headmaster')

@section('content')
<style>
.lds-ripple, .lds-ripple div { box-sizing: border-box; }
.lds-ripple { display:inline-block; position:relative; width:24px; height:24px; color:#10b981; }
.lds-ripple div { position:absolute; border:3px solid currentColor; opacity:1; border-radius:50%; animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; }
.lds-ripple div:nth-child(2) { animation-delay: -0.5s; }
@keyframes lds-ripple { 0% { top:10px; left:10px; width:4px; height:4px; opacity:0;} 4.9% { top:10px; left:10px; width:4px; height:4px; opacity:0;} 5% { top:10px; left:10px; width:4px; height:4px; opacity:1;} 100% { top:0; left:0; width:24px; height:24px; opacity:0; } }
.save-indicator { display:none; align-items:center; gap:6px; color:#10b981; font-size:12px; }
.save-indicator.show { display:inline-flex; }
</style>

<div class="max-w-7xl mx-auto">
  @if (session('success'))
    <div class="mb-3 p-3 rounded bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-3 p-3 rounded bg-amber-50 text-amber-800 ring-1 ring-amber-200">{{ session('error') }}</div>
  @endif

  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Manage Students</h1>
      <div class="text-slate-500 text-sm">Total: {{ $total }}</div>
        <select class="px-3 py-2 border rounded">
          <option value="">Stream</option>
          <option>A</option>
          <option>B</option>
          <option>C</option>
        </select>
      </div>
      <div class="text-slate-500 text-sm">Results: <span class="font-semibold">{{ $total ?? 0 }}</span></div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-2">#</th>
            <th class="text-left px-4 py-2">Admission No</th>
            <th class="text-left px-4 py-2">Name</th>
            <th class="text-left px-4 py-2">Gender</th>
            <th class="text-left px-4 py-2">Class</th>
            <th class="text-left px-4 py-2">Stream</th>
            <th class="text-left px-4 py-2">Created</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $i => $s)
            <tr class="border-t">
              <td class="px-4 py-2">{{ $i + 1 }}</td>
              <td class="px-4 py-2">{{ $s->admission_number ?? '—' }}</td>
              <td class="px-4 py-2">{{ $s->name ?? '—' }}</td>
              <td class="px-4 py-2">{{ $s->gender ?? '—' }}</td>
              <td class="px-4 py-2">{{ $s->class ?? '—' }}</td>
              <td class="px-4 py-2">{{ $s->stream ?? '—' }}</td>
              <td class="px-4 py-2 text-slate-500">{{ isset($s->created_at) ? (new \Carbon\Carbon($s->created_at))->diffForHumans() : '—' }}</td>
              @php $img = (isset($s->photo_path) && $s->photo_path) ? (\Illuminate\Support\Str::startsWith($s->photo_path,'http') ? $s->photo_path : asset('storage/'.$s->photo_path)) : asset('avatars/default.png'); @endphp
              <td class="px-4 py-2">{{ $img }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-6 text-center text-slate-500">No students found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
