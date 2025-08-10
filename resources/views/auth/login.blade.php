@extends('layouts.auth')

@section('title', 'eMAS | Sign In')

@section('content')
<div class="w-full max-w-md">
  <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl border-t-4 border-emas-green p-8">
    <div class="flex flex-col items-center mb-6">
      <img src="/logo-emas.svg" alt="eMAS" class="h-12 w-auto mb-3" />
      <h2 class="text-2xl font-bold tracking-wide text-gray-900">
        <span class="text-emas-green lowercase">e</span><span class="">MAS</span>
        <span class="text-base font-medium text-gray-500 align-middle">— Sign in</span>
      </h2>
    </div>
    <form method="POST" action="#" role="form" class="space-y-5">
      {{-- @csrf --}}
      <div>
        <label for="username" class="block text-sm font-semibold text-gray-900">Username</label>
        <div class="relative mt-1">
          <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
            <!-- User icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M10 2a4 4 0 100 8 4 4 0 000-8z"/><path fill-rule="evenodd" d="M.458 16.042A9.956 9.956 0 0110 12c3.042 0 5.78 1.353 7.542 3.542A1 1 0 0116.9 17.4 8.004 8.004 0 0010 14a8.004 8.004 0 00-6.9 3.4 1 1 0 01-1.642-1.358z" clip-rule="evenodd"/></svg>
          </span>
          <input type="text" id="username" name="username" required placeholder="Enter your username"
                 class="block w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green" />
        </div>
      </div>
      <div>
        <label for="password" class="block text-sm font-semibold text-gray-900">Password</label>
        <div class="relative mt-1">
          <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
            <!-- Lock icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 2a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7a5 5 0 00-5-5zm3 8H9V7a3 3 0 116 0v3z"/></svg>
          </span>
          <input type="password" id="password" name="password" required placeholder="Enter your password"
                 class="block w-full rounded-lg border border-gray-300 pl-10 pr-10 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green" />
          <button type="button" aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
            <!-- Eye icon (static placeholder, no JS yet) -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="w-full rounded-lg bg-emas-green hover:bg-emas-greenDark text-white font-semibold py-2.5 transition-colors shadow-sm">Sign In</button>
    </form>
    <div class="mt-5 flex items-center justify-between text-sm">
      <a href="{{ url('/password/forgot') }}" class="font-semibold text-emas-green hover:text-emas-greenDark">Forgot password?</a>
      <span class="text-gray-400">•</span>
      <span class="text-gray-500">Need access? Contact Admin</span>
    </div>
  </div>
</div>
@endsection
