@extends('layouts.headmaster')

@section('title','Proposal Details | Headmaster')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-regular fa-file-lines"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">{{ $proposal->title ?? ('Proposal #'.$proposal->id) }}</h1>
        <div class="text-slate-500 text-sm">Created {{ isset($proposal->created_at) ? \Carbon\Carbon::parse($proposal->created_at)->diffForHumans() : '' }}</div>
      </div>
    </div>
    <a href="{{ route('headmaster.teachers.proposals') }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
  </div>

  @if(!empty($proposal->notes))
  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4 mb-4">
    <div class="font-medium mb-1">Notes</div>
    <div class="text-slate-700 whitespace-pre-line">{{ $proposal->notes }}</div>
  </div>
  @endif

  <div class="bg-white rounded-xl ring-1 ring-slate-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        @php($st = strtolower($proposal->status ?? 'pending'))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
          @if($st==='selected') bg-green-50 text-green-700 @elseif($st==='rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">
          <i class="fa-solid @if($st==='selected') fa-circle-check @elseif($st==='rejected') fa-circle-xmark @else fa-hourglass-half @endif"></i>
          <span class="capitalize">{{ $st }}</span>
        </span>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-semibold px-4 py-2">Teacher</th>
            <th class="text-left font-semibold px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $row)
            <tr class="border-t">
              <td class="px-4 py-2">{{ $row->teacher_name ?? ('Teacher #'.$row->teacher_id) }}</td>
              <td class="px-4 py-2">
                @php($rs = strtolower($row->status ?? 'pending'))
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
                  @if($rs==='selected') bg-green-50 text-green-700 @elseif($rs==='rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">
                  <i class="fa-solid @if($rs==='selected') fa-circle-check @elseif($rs==='rejected') fa-circle-xmark @else fa-hourglass-half @endif"></i>
                  <span class="capitalize">{{ $rs }}</span>
                </span>
              </td>
            </tr>
          @empty
            <tr><td class="px-4 py-6 text-center text-slate-500" colspan="2">No teachers attached to this proposal.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
