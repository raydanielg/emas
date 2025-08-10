@extends('layouts.auth')

@section('title', 'EMAS | Sahau Neno la Siri')

@section('content')
<div class="w-full max-w-md">
  <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl border-t-4 border-emas-green p-8">
    <div class="flex flex-col items-center mb-6">
      <img src="/logo-emas.svg" alt="eMAS" class="h-12 w-auto mb-3" />
      <h2 class="text-2xl font-bold tracking-wide text-gray-900 text-center">
        <span class="text-emas-green lowercase">e</span><span class="">MAS</span>
        <span class="text-base font-medium text-gray-500 align-middle">— Rudisha Neno la Siri</span>
      </h2>
    </div>
    <form method="POST" action="#" role="form" class="space-y-4">
      {{-- @csrf --}}
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-900">Barua Pepe</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green" />
      </div>
      <button type="submit" class="w-full rounded-lg bg-emas-green hover:bg-emas-greenDark text-white font-semibold py-2.5 transition-colors shadow-sm">Tuma kiungo cha kurekebisha</button>
    </form>
    <p class="text-center text-sm text-gray-600 mt-5">
      <a href="{{ url('/login') }}" class="font-semibold text-emas-yellow hover:underline">Rudi kwenye kuingia</a>
    </p>
  </div>
</div>
@endsection
