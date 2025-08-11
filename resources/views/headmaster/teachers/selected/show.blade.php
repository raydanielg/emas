@extends('layouts.headmaster')

@section('title','Selection Details | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-clipboard-check"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">{{ $selection->title ?? ('Selection #'.$selection->id) }}</h1>
        <div class="text-slate-500 text-sm">Created {{ isset($selection->created_at) ? \Carbon\Carbon::parse($selection->created_at)->diffForHumans() : '' }}</div>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('headmaster.teachers.selected') }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
      <form method="post" action="{{ route('headmaster.teachers.selected.generate_letter', $selection->id) }}">
        @csrf
        <button class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90"><i class="fa-solid fa-file-lines"></i><span>Generate Letter</span></button>
      </form>
      @if(!empty($selection->letter_generated_at))
        <a href="{{ route('headmaster.teachers.selected.letter', $selection->id) }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-slate-50"><i class="fa-regular fa-eye"></i><span>View Letter</span></a>
      @endif
    </div>
  </div>

  @if(!empty($selection->notes))
  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4 mb-4">
    <div class="font-medium mb-1">Notes</div>
    <div class="text-slate-700 whitespace-pre-line">{{ $selection->notes }}</div>
  </div>
  @endif

  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-semibold px-4 py-2">Teacher</th>
            <th class="text-left font-semibold px-4 py-2">Role</th>
            <th class="text-left font-semibold px-4 py-2">Assigned As</th>
            <th class="text-left font-semibold px-4 py-2">Status</th>
            <th class="text-left font-semibold px-4 py-2">Comment</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $row)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $row->teacher_name ?? ('Teacher #'.$row->teacher_id) }}</td>
            <td class="px-4 py-2">{{ ucfirst($row->role ?? '') }}</td>
            <td class="px-4 py-2">{{ $row->assigned_as ?? '' }}</td>
            <td class="px-4 py-2">
              @php($rs = strtolower($row->status ?? 'selected'))
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
                @if($rs==='selected') bg-green-50 text-green-700 @elseif($rs==='rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">
                <i class="fa-solid @if($rs==='selected') fa-circle-check @elseif($rs==='rejected') fa-circle-xmark @else fa-hourglass-half @endif"></i>
                <span class="capitalize">{{ $rs }}</span>
              </span>
            </td>
            <td class="px-4 py-2 text-slate-600">{{ $row->comment ?? '' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-slate-500">No teachers in this selection yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
