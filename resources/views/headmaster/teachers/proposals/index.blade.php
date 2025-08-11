@extends('layouts.headmaster')

@section('title','Teacher Proposals | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-regular fa-file-lines"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Teacher Proposals</h1>
        <div class="text-slate-500 text-sm">See the latest proposals and their status</div>
      </div>
    </div>
    <button onclick="openAddProposal()" class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
      <i class="fa-solid fa-plus"></i>
      <span>Add Proposal</span>
    </button>
  </div>

  @if(session('success'))
    <div class="mb-3 px-3 py-2 rounded bg-green-50 text-green-700 border border-green-200">
      <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 border border-red-200">
      <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
  @endif

  @if($proposals->count() === 0)
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-8 text-center">
      <div class="mx-auto h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-500"><i class="fa-regular fa-file-lines"></i></div>
      <div class="mt-2 font-semibold">No proposals yet</div>
      <div class="text-slate-500 text-sm">Click "Add Proposal" to create the first one.</div>
    </div>
  @else
  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-slate-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Title</th>
          <th class="text-left font-semibold px-4 py-2">Created</th>
          <th class="text-left font-semibold px-4 py-2">Status</th>
          <th class="text-left font-semibold px-4 py-2">Counts</th>
          <th class="text-left font-semibold px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($proposals as $p)
          <tr class="border-t">
            <td class="px-4 py-2">
              <div class="font-medium">{{ $p->title ?? ('Proposal #'.$p->id) }}</div>
              @if(!empty($p->notes))
                <div class="text-xs text-slate-500 line-clamp-1">{{ $p->notes }}</div>
              @endif
            </td>
            <td class="px-4 py-2 text-slate-600">{{ isset($p->created_at) ? \Carbon\Carbon::parse($p->created_at)->diffForHumans() : '—' }}</td>
            <td class="px-4 py-2">
              @php($st = strtolower($p->status ?? 'pending'))
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
                @if($st==='selected') bg-green-50 text-green-700 @elseif($st==='rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">
                <i class="fa-solid @if($st==='selected') fa-circle-check @elseif($st==='rejected') fa-circle-xmark @else fa-hourglass-half @endif"></i>
                <span class="capitalize">{{ $st }}</span>
              </span>
            </td>
            <td class="px-4 py-2 text-slate-600">
              <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-group text-slate-400"></i> {{ $p->count_total ?? 0 }}</span>
                <span class="inline-flex items-center gap-1 text-green-700"><i class="fa-solid fa-circle-check"></i> {{ $p->count_selected ?? 0 }}</span>
                <span class="inline-flex items-center gap-1 text-amber-700"><i class="fa-solid fa-hourglass-half"></i> {{ $p->count_pending ?? 0 }}</span>
                <span class="inline-flex items-center gap-1 text-red-700"><i class="fa-solid fa-circle-xmark"></i> {{ $p->count_rejected ?? 0 }}</span>
              </div>
            </td>
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                <a href="{{ route('headmaster.teachers.proposals.show', $p->id) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs border rounded hover:bg-slate-50"><i class="fa-regular fa-eye"></i><span>View</span></a>
                {{-- No delete here as requested --}}
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- Add Proposal Modal --}}
<div id="add-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeAddProposal()"></div>
  <div class="relative mx-auto mt-12 w-full max-w-3xl">
    <div class="bg-white rounded-xl ring-1 ring-slate-200 p-6 shadow-lg">
      <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-full bg-emas-green/10 text-emas-green flex items-center justify-center"><i class="fa-regular fa-file-lines"></i></div>
        <div>
          <div class="text-lg font-semibold">Add Proposal</div>
          <div class="text-slate-500 text-sm">Provide a title, notes, and select teachers</div>
        </div>
      </div>
      <form method="post" action="{{ route('headmaster.teachers.proposals.store') }}" onsubmit="setAddLoading(true)">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-sm mb-1">Title</label>
            <input type="text" name="title" class="border rounded px-3 py-2 w-full" required>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm mb-1">Notes (optional)</label>
            <textarea name="notes" rows="3" class="border rounded px-3 py-2 w-full" placeholder="Describe this proposal..."></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm mb-1">Select Teachers</label>
            <div class="max-h-64 overflow-auto border rounded p-3">
              @foreach($teachers as $t)
                <label class="flex items-center gap-2 py-1">
                  <input type="checkbox" name="teacher_ids[]" value="{{ $t->id }}" class="rounded">
                  <span>{{ $t->display_name }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button type="button" class="px-3 py-2 border rounded hover:bg-slate-50" onclick="closeAddProposal()">Cancel</button>
          <button id="add-btn" class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">
            <span class="add-icon"><i class="fa-solid fa-floppy-disk"></i></span>
            <span class="add-text">Save</span>
            <span class="add-loading hidden"><i class="fa-solid fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openAddProposal(){ document.getElementById('add-modal').classList.remove('hidden'); }
  function closeAddProposal(){ document.getElementById('add-modal').classList.add('hidden'); setAddLoading(false); }
  function setAddLoading(state){
    const btn = document.getElementById('add-btn');
    const icon = btn.querySelector('.add-icon');
    const txt = btn.querySelector('.add-text');
    const load = btn.querySelector('.add-loading');
    if(state){ btn.setAttribute('disabled','disabled'); icon.classList.add('hidden'); txt.textContent='Saving...'; load.classList.remove('hidden'); }
    else { btn.removeAttribute('disabled'); icon.classList.remove('hidden'); txt.textContent='Save'; load.classList.add('hidden'); }
  }
</script>
@endsection
