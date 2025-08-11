@extends('layouts.user')
@section('title','Student Profile | Marking | eMAS')
@section('content')
<div class="max-w-7xl mx-auto">
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Student Profile</h1>
    <div class="flex items-center gap-2">
      <a href="{{ route('marking.students') }}" class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm show-loader">List Students</a>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4">
    <!-- Left card -->
    <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
      <div class="aspect-[4/5] w-full rounded border border-dashed grid place-content-center text-slate-400">NO IMAGE AVAILABLE</div>
      <div class="mt-3">
        <div class="font-semibold">{{ ($student->first_name ?? '') }} {{ ($student->last_name ?? '') }}</div>
        <div class="text-slate-600 text-sm">Exam#: {{ $student->exam_number ?? '—' }}</div>
      </div>
      <dl class="mt-4 text-sm space-y-2">
        <div class="flex justify-between"><dt class="text-slate-500">Sex</dt><dd>{{ $student->sex ?? '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-500">Form</dt><dd>{{ $student->form ?? '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-500">School</dt><dd>{{ $student->school_name ?? '—' }}</dd></div>
      </dl>
    </div>

    <!-- Right content: read-only marks -->
    <div class="md:col-span-2 bg-white rounded-xl ring-1 ring-gray-200">
      <div class="border-b px-4 py-2 flex gap-2 text-sm">
        <div class="px-3 py-1 rounded bg-slate-100">Marks</div>
      </div>
      <div class="p-4">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
              <tr>
                <th class="text-left font-semibold px-4 py-2">Subject</th>
                <th class="text-left font-semibold px-4 py-2">Form</th>
                <th class="text-left font-semibold px-4 py-2">Score</th>
                <th class="text-left font-semibold px-4 py-2">Entered By</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($marks as $mk)
              <tr class="border-t">
                <td class="px-4 py-2">{{ $mk->subject_name }} ({{ $mk->subject_code }})</td>
                <td class="px-4 py-2">{{ $mk->form }}</td>
                <td class="px-4 py-2">{{ $mk->score }}</td>
                <td class="px-4 py-2">{{ $mk->entered_by ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No marks recorded.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
