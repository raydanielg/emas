@extends('layouts.user')
@section('title','Students | Marking | eMAS')
@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Students</h1>
    <form method="get" action="{{ route('marking.students') }}" class="flex items-center gap-2" data-show-loader>
      <input name="q" value="{{ $filters['q'] ?? '' }}" type="text" placeholder="Search name or exam no." class="px-3 py-2 rounded-lg border border-gray-200 text-sm" />
      <select name="school_id" class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
        <option value="">All schools</option>
        @foreach ($schools as $sch)
          <option value="{{ $sch->id }}" @selected(($filters['school_id'] ?? '') == $sch->id)>{{ $sch->name }}</option>
        @endforeach
      </select>
      <select name="form" class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
        <option value="">All forms</option>
        @php($romans = [1=>'I',2=>'II',3=>'III',4=>'IV'])
        @foreach ($romans as $num => $roman)
          <option value="{{ $num }}" @selected((string)($filters['form'] ?? '') === (string)$num)>Form {{ $roman }}</option>
        @endforeach
      </select>
      <button class="px-3 py-2 rounded-lg bg-emas-green text-white text-sm">Filter</button>
    </form>
  </div>

  <div class="overflow-x-auto bg-white rounded-xl ring-1 ring-gray-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left font-semibold px-4 py-2">Exam Number</th>
          <th class="text-left font-semibold px-4 py-2">Full Name</th>
          <th class="text-left font-semibold px-4 py-2">Sex</th>
          <th class="text-left font-semibold px-4 py-2">Form</th>
          <th class="text-left font-semibold px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($students as $row)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $row->exam_number ?? '—' }}</td>
          <td class="px-4 py-2">
            <a href="{{ route('marking.students.show', ['id' => $row->id]) }}" class="text-emas-green hover:underline show-loader">
              {{ $row->last_name }}, {{ $row->first_name }}
            </a>
          </td>
          <td class="px-4 py-2">{{ $row->sex }}</td>
          <td class="px-4 py-2">Form {{ $romans[$row->form] ?? $row->form }}</td>
          <td class="px-4 py-2 text-slate-500 space-x-2">
            <a href="{{ route('marking.students.show', ['id' => $row->id]) }}" title="View" class="hover:text-slate-700 show-loader">👁️</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No students found for your assignment.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($pagination && $pagination['last_page'] > 1)
  <div class="mt-4 flex items-center justify-between text-sm">
    <?php 
      $params = request()->query();
      $prev = max(1, $pagination['current_page'] - 1);
      $next = min($pagination['last_page'], $pagination['current_page'] + 1);
      $qp = fn($p) => http_build_query(array_merge($params, ['page'=>$p]));
    ?>
    <a class="px-3 py-2 rounded border border-gray-200 bg-white @if($pagination['current_page']==1) pointer-events-none opacity-50 @endif show-loader" href="?{{ $qp($prev) }}">Previous</a>
    <div>Page {{ $pagination['current_page'] }} of {{ $pagination['last_page'] }}</div>
    <a class="px-3 py-2 rounded border border-gray-200 bg-white @if($pagination['current_page']==$pagination['last_page']) pointer-events-none opacity-50 @endif show-loader" href="?{{ $qp($next) }}">Next</a>
  </div>
  @endif
</div>
@endsection
