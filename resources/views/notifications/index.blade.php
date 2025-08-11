@extends('layouts.user')
@section('title','Notifications')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Inbox</h1>
    <form method="GET" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
      <button class="px-3 py-2 rounded-md bg-emas-green text-white text-sm">Search</button>
    </form>
  </div>

  <div class="overflow-x-auto bg-white ring-1 ring-gray-200 rounded-md">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-slate-700">
        <tr>
          <th class="text-left px-4 py-2">Title</th>
          <th class="text-left px-4 py-2">Created</th>
          <th class="text-left px-4 py-2">Status</th>
          <th class="text-left px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($notifications as $n)
        <tr class="border-t hover:bg-slate-50">
          <td class="px-4 py-2">
            <a href="{{ route('notifications.show',$n->id) }}" class="text-emas-green font-medium hover:underline">{{ $n->title }}</a>
            <div class="text-slate-500 line-clamp-1">{{ Str::limit($n->message, 140) }}</div>
          </td>
          <td class="px-4 py-2 text-slate-600">{{ \Carbon\Carbon::parse($n->created_at)->format('M d, Y H:i') }}</td>
          <td class="px-4 py-2">
            @if(!$n->read_at)
              <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Unread</span>
            @else
              <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Read</span>
            @endif
          </td>
          <td class="px-4 py-2">
            <a class="text-slate-500 hover:text-slate-700" href="{{ route('notifications.show',$n->id) }}" title="View"><i class="fa-solid fa-eye"></i></a>
          </td>
        </tr>
        @empty
        <tr><td class="px-4 py-8 text-center text-slate-500" colspan="4">No notifications</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
