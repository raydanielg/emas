@extends('layouts.admin')

@section('title','Admin | Users')
@section('page_title','Users')

@section('content')
<div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
  <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-4 rounded">
    <div class="text-sm opacity-90">Roles Summary</div>
    <div class="mt-2 flex flex-wrap gap-2">
      @forelse($rolesSummary as $r)
        <span class="px-2 py-1 rounded bg-white/20">{{ $r->role }}: <b>{{ $r->total }}</b></span>
      @empty
        <span class="opacity-80">No data</span>
      @endforelse
    </div>
  </div>
  <div class="bg-white ring-1 ring-slate-200 p-4 rounded md:col-span-2">
    <form method="GET" class="flex gap-2 items-end">
      <div>
        <label class="block text-xs text-slate-500">Filter by role</label>
        <input type="text" name="role" value="{{ request('role') }}" placeholder="e.g. enterer, headmaster, admin" class="mt-1 w-56 rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
      </div>
      <button class="px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">Apply</button>
      @if(request('role'))
        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded border">Clear</a>
      @endif
    </form>
  </div>
</div>

<div class="bg-white ring-1 ring-slate-200 rounded overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="bg-slate-50 text-slate-600">
        <th class="text-left p-3">Name</th>
        <th class="text-left p-3">Email</th>
        <th class="text-left p-3">Username</th>
        <th class="text-left p-3">Role</th>
        <th class="text-left p-3">Created</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $u)
        <tr class="border-t border-slate-100 hover:bg-emerald-50/30">
          <td class="p-3">{{ $u->name }}</td>
          <td class="p-3">{{ $u->email }}</td>
          <td class="p-3">{{ $u->username }}</td>
          <td class="p-3">
            <span class="px-2 py-1 rounded text-xs {{ $u->role==='admin' || $u->role==='superadmin' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ $u->role ?: '(none)' }}</span>
          </td>
          <td class="p-3">{{ 
            optional($u->created_at)->format('Y-m-d') ?? ''
          }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="p-6 text-center text-slate-500">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
