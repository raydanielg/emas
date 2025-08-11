@extends('layouts.admin')

@section('title','Admin | Institutions')
@section('page_title','Institutions (Registration)')

@section('content')
<div class="mb-4 flex items-center justify-between">
  <div class="text-sm text-slate-600">Showing latest institutions. Use search to filter.</div>
  <div>
    <input id="inst-search" type="text" placeholder="Search name/code/region/district" class="w-72 rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
  </div>
</div>

<div class="bg-white ring-1 ring-slate-200 rounded overflow-hidden">
  <table class="min-w-full text-sm" id="inst-table">
    <thead>
      <tr class="bg-slate-50 text-slate-600">
        <th class="text-left p-3">Code</th>
        <th class="text-left p-3">Name</th>
        <th class="text-left p-3">District</th>
        <th class="text-left p-3">Region</th>
        <th class="text-left p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($schools as $s)
        <tr class="border-t border-slate-100 hover:bg-emerald-50/20">
          <td class="p-3 font-mono">{{ $s->code }}</td>
          <td class="p-3">{{ $s->name }}</td>
          <td class="p-3">{{ $s->district }}</td>
          <td class="p-3">{{ $s->region }}</td>
          <td class="p-3">
            <button class="px-2 py-1 rounded border hover:bg-slate-50 text-slate-700"><i class="fa-solid fa-eye"></i> View</button>
            <button class="px-2 py-1 rounded border hover:bg-slate-50 text-slate-700"><i class="fa-solid fa-pen"></i> Edit</button>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="p-6 text-center text-slate-500">No institutions found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
  const input = document.getElementById('inst-search');
  const table = document.getElementById('inst-table');
  input?.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    for (const tr of table.querySelectorAll('tbody tr')) {
      const text = tr.innerText.toLowerCase();
      tr.style.display = text.includes(q) ? '' : 'none';
    }
  });
</script>
@endsection
