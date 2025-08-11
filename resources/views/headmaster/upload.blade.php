@extends('layouts.headmaster')

@section('title','Upload Students | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Upload Students CSV</h1>
    <p class="text-slate-600">CSV with header: <code>full_name,admission_no,form_level,stream,gender,dob</code></p>
  </div>

  <form action="{{ route('headmaster.upload.store') }}" method="post" enctype="multipart/form-data" class="bg-white rounded-xl ring-1 ring-gray-200 p-4 space-y-4" data-show-loader>
    @csrf

    <div>
      <label class="block text-sm font-medium mb-1">Form Level (default for rows without value)</label>
      <input type="text" name="form_level" class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" placeholder="Form 1" />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">CSV File</label>
      <input type="file" name="csv" accept=".csv,text/csv" required class="block w-full" />
      @error('csv') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('headmaster.dashboard') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
      <button class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark">Upload</button>
    </div>
  </form>

  <div class="mt-8">
    <h2 class="font-semibold mb-2">Sample CSV</h2>
    <pre class="bg-slate-900 text-slate-100 p-3 rounded text-sm overflow-x-auto">full_name,admission_no,form_level,stream,gender,dob
John Doe,ADM001,Form 2,A,M,2008-05-10
Jane Smith,ADM002,Form 2,B,F,2008-08-20
    </pre>
  </div>
</div>
@endsection
