@extends('layouts.headmaster')

@section('title','Create Request — Students | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="mb-4">
    <h1 class="text-2xl font-bold">Send Request</h1>
    <p class="text-slate-500">Tuma ombi kwa maandishi: kuomba matokeo, marekebisho ya mfumo/taarifa, au changamoto mbalimbali.</p>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 mb-6">
    <form method="POST" action="{{ route('headmaster.reports.requests.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @if(($schools ?? collect())->count())
      <div>
        <label class="block text-sm font-medium text-slate-700">School (optional)</label>
        <select name="school_id" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green">
          <option value="">— Any —</option>
          @foreach($schools as $s)
            <option value="{{ $s->id }}">{{ $s->name }} @if(!empty($s->code)) ({{ $s->code }}) @endif</option>
          @endforeach
        </select>
      </div>
      @endif
      <div>
        <label class="block text-sm font-medium text-slate-700">Category</label>
        <select name="category" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" required>
          <option value="results">Results Request</option>
          <option value="system_correction">System Correction</option>
          <option value="data_correction">Data Correction</option>
          <option value="issue">Issue / Challenge</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Message</label>
        <textarea name="message" rows="4" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" placeholder="Eleza ombi lako kwa ufupi na wazi..." required></textarea>
      </div>
      <div class="md:col-span-2 flex items-center gap-3">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-emas-green text-white hover:bg-emerald-600">
          <i class="fa-solid fa-paper-plane"></i>
          <span>Submit</span>
        </button>
        <a href="{{ route('headmaster.reports.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back</span>
        </a>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold">My Requests</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-2">#</th>
            <th class="text-left px-4 py-2">Type</th>
            <th class="text-left px-4 py-2">Quantity</th>
            <th class="text-left px-4 py-2">School</th>
            <th class="text-left px-4 py-2">Status</th>
            <th class="text-left px-4 py-2">Created</th>
            <th class="text-left px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody id="draft-rows">
          <!-- Draft rows injected here -->
        </tbody>
        <tbody>
          @forelse(($requests ?? collect()) as $r)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $r->id }}</td>
            <td class="px-4 py-2">{{ $r->type ?? 'student_count' }}</td>
            <td class="px-4 py-2">{{ $r->quantity ?? '-' }}</td>
            <td class="px-4 py-2">{{ $r->school_name ?? '' }} @if(!empty($r->school_code))<span class="text-slate-500">({{ $r->school_code }})</span>@endif</td>
            <td class="px-4 py-2">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs
                @if(($r->status ?? '')==='approved') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
                @elseif(($r->status ?? '')==='need_approval') bg-amber-50 text-amber-700 ring-1 ring-amber-200
                @elseif(($r->status ?? '')==='cancelled') bg-rose-50 text-rose-700 ring-1 ring-rose-200
                @else bg-slate-100 text-slate-700 ring-1 ring-slate-200 @endif">{{ $r->status ?? '-' }}</span>
            </td>
            <td class="px-4 py-2">{{ 
              isset($r->created_at) ? (\Carbon\Carbon::parse($r->created_at)->format('Y-m-d H:i')) : '-' 
            }}</td>
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                <a href="{{ route('headmaster.reports.requests.show', $r->id) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded border border-slate-300 hover:bg-slate-50">
                  <i class="fa-regular fa-eye"></i> <span>View</span>
                </a>
                @if(($r->status ?? '')!=='approved' && ($r->status ?? '')!=='cancelled')
                <form method="POST" action="{{ route('headmaster.reports.requests.cancel', $r->id) }}" onsubmit="return confirm('Cancel this request?');">
                  @csrf
                  <button class="inline-flex items-center gap-1 px-3 py-1 rounded border border-rose-300 text-rose-700 hover:bg-rose-50">
                    <i class="fa-regular fa-circle-xmark"></i> <span>Cancel</span>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-slate-500">No requests yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  
</div>
@endsection
