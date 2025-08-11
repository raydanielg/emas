@extends('layouts.headmaster')

@section('title','My Requests — Approved | eMAS')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Approved Requests <span class="text-slate-400">— Maombi yaliyoidhinishwa</span></h1>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-500">
            <th class="py-2 px-4">Ref</th>
            <th class="py-2 px-4">Student</th>
            <th class="py-2 px-4">Type</th>
            <th class="py-2 px-4">Approved</th>
            <th class="py-2 px-4">Comment</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $r)
            <tr class="border-t">
              <td class="py-2 px-4">#{{ $r->id }}</td>
              <td class="py-2 px-4">{{ $r->student_name ?? '-' }}</td>
              <td class="py-2 px-4">{{ $r->type ?? 'Request' }}</td>
              <td class="py-2 px-4">{{ \Carbon\Carbon::parse($r->updated_at ?? $r->created_at)->diffForHumans() }}</td>
              <td class="py-2 px-4">{{ $r->admin_comment ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-slate-500">No approved requests.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
