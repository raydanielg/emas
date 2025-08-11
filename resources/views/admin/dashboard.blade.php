@extends('layouts.admin')

@section('title','Admin | Dashboard')
@section('page_title','Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="bg-white p-4 rounded ring-1 ring-slate-200">
    <div class="text-slate-500 text-sm">Total Users</div>
    <div class="text-2xl font-semibold">--</div>
  </div>
  <div class="bg-white p-4 rounded ring-1 ring-slate-200">
    <div class="text-slate-500 text-sm">Schools</div>
    <div class="text-2xl font-semibold">--</div>
  </div>
  <div class="bg-white p-4 rounded ring-1 ring-slate-200">
    <div class="text-slate-500 text-sm">Reports</div>
    <div class="text-2xl font-semibold">--</div>
  </div>
</div>
@endsection
