@extends('layouts.headmaster')

@section('title','My Profile | Headmaster')

@section('content')
@php $u = auth()->user(); @endphp
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">My Profile</h1>

  @if (session('status'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700 ring-1 ring-green-200">{{ session('status') }}</div>
  @endif
  @if (session('welcome_headmaster'))
    <div class="mb-4 p-3 rounded bg-amber-50 text-amber-800 ring-1 ring-amber-200">
      Karibu kwenye eMAS Headmaster Panel. Tafadhali kamilisha taarifa zako na unaweza kubadilisha nenosiri hapa hapa.
    </div>
  @endif

  <form action="{{ route('headmaster.profile.update') }}" method="post" class="bg-white rounded-lg ring-1 ring-slate-200 p-5 space-y-6" data-show-loader>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1">
        <div class="flex items-center gap-4">
          @php $src = ($u->avatar_path ?? null) ? (str_starts_with($u->avatar_path,'profiles/') ? asset('storage/'.$u->avatar_path) : asset($u->avatar_path)) : null; @endphp
          @if ($src)
            <img src="{{ $src }}" alt="Avatar" class="h-20 w-20 rounded-full object-cover ring-2 ring-slate-200">
          @else
            <div class="h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center ring-2 ring-slate-200">
              <span class="text-2xl font-semibold">{{ strtoupper(substr($u->name ?? 'U',0,1)) }}</span>
            </div>
          @endif
          <div>
            <div class="text-sm text-slate-600">Choose Google avatar:</div>
            <div class="mt-2 grid grid-cols-4 gap-2">
              @php $current = $u->avatar_path; @endphp
              @foreach(($presets ?? []) as $p)
                <label class="cursor-pointer">
                  <input type="radio" name="avatar_choice" value="{{ $p }}" class="peer hidden" @checked($current === $p)>
                  <div class="p-1 rounded ring-1 ring-slate-200 peer-checked:ring-2 peer-checked:ring-emas-green">
                    <img src="{{ asset($p) }}" alt="Avatar" class="h-10 w-10">
                  </div>
                </label>
              @endforeach
              <label class="cursor-pointer">
                <input type="radio" name="avatar_choice" value="none" class="peer hidden" @checked(empty($current))>
                <div class="h-10 w-10 rounded flex items-center justify-center text-xs bg-slate-50 ring-1 ring-slate-200 peer-checked:ring-2 peer-checked:ring-emas-green">None</div>
              </label>
            </div>
            <p class="text-xs text-slate-500 mt-2">Avatars are preset only.</p>
          </div>
        </div>
      </div>
      <div class="lg:col-span-2 space-y-4">
        <div>
          <label class="block text-sm text-slate-600 mb-1">Full Name</label>
          <input type="text" name="name" value="{{ old('name', $u->name) }}" class="w-full px-3 py-2 border rounded @error('name') border-red-500 @enderror" required>
          @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Phone (TZ)</label>
            <input type="tel" name="phone" value="{{ old('phone', $u->phone) }}" placeholder="e.g. 07XXXXXXXX or +2557XXXXXXXX" pattern="^(\+255|0)(6|7)\d{8}$" class="w-full px-3 py-2 border rounded @error('phone') border-red-500 @enderror">
            @error('phone')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $u->date_of_birth) }}" class="w-full px-3 py-2 border rounded @error('date_of_birth') border-red-500 @enderror">
            @error('date_of_birth')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        <div>
          <label class="block text-sm text-slate-600 mb-1">Bank Number</label>
          <input type="text" name="bank_number" value="{{ old('bank_number', $u->bank_number) }}" class="w-full px-3 py-2 border rounded @error('bank_number') border-red-500 @enderror">
          @error('bank_number')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    <div class="bg-slate-50 rounded p-4 text-sm text-slate-700">
      @php $meta = $schoolMeta ?? []; $primary = $meta[0] ?? null; @endphp
      @if($primary)
        <div class="mb-4 p-3 rounded bg-white ring-1 ring-emas-green/30">
          <div class="text-xs uppercase tracking-wide text-slate-500">Primary School Code</div>
          <div class="mt-1 flex items-center gap-2">
            <span class="text-base font-semibold">{{ $primary['name'] }}</span>
            @if(!empty($primary['code']))
              <span class="px-2 py-0.5 rounded-full text-xs bg-emas-green/10 text-emas-green ring-1 ring-emas-green/30">Code: {{ $primary['code'] }}</span>
            @else
              <span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800 ring-1 ring-amber-200">No code set</span>
            @endif
          </div>
          <p class="text-xs text-slate-500 mt-1">Admission numbers use format: S.{code}.NNNN (example: S.{{ $primary['code'] ?? 'SCHOOL' }}.0001).</p>
        </div>
      @endif
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><span class="font-semibold">Role:</span> Headmaster</div>
        <div><span class="font-semibold">Email/Username:</span> {{ $u->email ?? $u->username }}</div>
        <div><span class="font-semibold">Institution:</span> {{ $u->institution ?? '-' }}</div>
      </div>
      <div class="mt-3">
        <div class="font-semibold mb-1">Assigned Schools</div>
        @if (!empty($schoolMeta))
          <div class="flex flex-wrap gap-2">
            @foreach($schoolMeta as $s)
              <span class="px-2 py-1 rounded-full text-xs bg-white ring-1 ring-slate-200">
                {{ $s['name'] }}
                @if(!empty($s['code']))
                  <span class="ml-1 text-slate-500">(Code: {{ $s['code'] }})</span>
                @else
                  <span class="ml-1 text-amber-600">(No code)</span>
                @endif
              </span>
            @endforeach
          </div>
        @else
          <div class="text-slate-500 text-sm">No assigned schools found.</div>
        @endif
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button class="px-4 py-2 rounded bg-emas-green text-white hover:bg-emas-green/90">Save Changes</button>
      <a href="{{ route('headmaster.dashboard') }}" class="px-4 py-2 rounded bg-slate-100 hover:bg-slate-200">Cancel</a>
    </div>
  </form>

  <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg ring-1 ring-slate-200 p-5">
      <h2 class="font-semibold mb-3">Change Password</h2>
      <form action="{{ route('headmaster.password.update') }}" method="post" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm text-slate-600 mb-1">Current Password</label>
          <input type="password" name="current_password" class="w-full px-3 py-2 border rounded @error('current_password') border-red-500 @enderror" required>
          @error('current_password')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm text-slate-600 mb-1">New Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror" required>
            @error('password')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded" required>
          </div>
        </div>
        <button class="px-4 py-2 rounded bg-slate-800 text-white hover:bg-slate-700">Update Password</button>
      </form>
    </div>
    <div class="bg-white rounded-lg ring-1 ring-slate-200 p-5">
      <h2 class="font-semibold mb-3">Suggest Correction (Wrong School Assignment)</h2>
      <form action="{{ route('headmaster.suggestion.store') }}" method="post" class="space-y-3">
        @csrf
        <textarea name="message" rows="4" class="w-full px-3 py-2 border rounded" placeholder="Explain the issue, e.g., I am assigned to XYZ but should be ABC..." required>{{ old('message') }}</textarea>
        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Send Suggestion</button>
      </form>
      <p class="text-xs text-slate-500 mt-2">Tutapokea maoni yako na kuchunguza uteuzi wa shule uliopewa.</p>
    </div>
  </div>
</div>
@endsection
