@extends('layouts.user')

@section('title','Settings | eMAS')

@section('content')
<div class="max-w-4xl mx-auto grid gap-6">
  @if ($errors->any())
    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg">
      <div class="font-semibold mb-1">Please fix the following:</div>
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @if (session('status'))
    <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-lg">{{ session('status') }}</div>
  @endif

  @php
    $incomplete = empty($user->phone) || empty($user->bank_number) || empty($user->date_of_birth);
  @endphp
  @if ($incomplete)
    <div class="bg-amber-50 text-amber-800 px-4 py-3 rounded-lg">
      <div class="font-semibold">Please complete your profile</div>
      <div class="text-sm">Add your phone number, bank number, and date of birth to complete your profile.</div>
    </div>
  @endif

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
    <h2 class="font-semibold mb-4">Profile</h2>
    <form action="{{ route('settings.profile.update') }}" method="post" class="grid gap-5" data-show-loader enctype="multipart/form-data">
      @csrf
      <div class="flex flex-col sm:flex-row gap-6">
        <div class="shrink-0">
          <div class="w-28 h-28 rounded-full overflow-hidden ring-2 ring-emas-green/20 bg-slate-100">
            @if ($user->avatar_path)
              <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
            @else
              <div class="w-full h-full flex items-center justify-center text-slate-400">
                <i class="fa-regular fa-user text-4xl"></i>
              </div>
            @endif
          </div>
          <label class="mt-3 inline-flex items-center gap-2 text-sm cursor-pointer">
            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
            <span class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Upload Photo</span>
          </label>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
          <div class="grid gap-1">
            <label class="text-sm text-slate-600">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required>
          </div>
          <div class="grid gap-1">
            <label class="text-sm text-slate-600">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required>
          </div>
          <div class="grid gap-1">
            <label class="text-sm text-slate-600">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +255 712 345 678" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green">
          </div>
          <div class="grid gap-1">
            <label class="text-sm text-slate-600">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green">
          </div>
          <div class="grid gap-1 sm:col-span-2">
            <label class="text-sm text-slate-600">Bank Number</label>
            <input type="text" name="bank_number" value="{{ old('bank_number', $user->bank_number) }}" placeholder="Enter your bank account number" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green">
          </div>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="grid gap-2">
          <div class="text-sm text-slate-600">Role / Privilege</div>
          <div>
            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
              <i class="fa-solid fa-user-shield text-slate-500"></i>
              {{ ucfirst($user->role ?? 'enterer') }}
            </span>
          </div>
          <div class="text-xs text-slate-500">Examples: enterer, chairperson, admin, superadmin, or any assigned role.</div>
        </div>
        <div class="grid gap-2">
          <div class="text-sm text-slate-600">Institution (Origin)</div>
          <input type="text" name="institution" value="{{ old('institution', $user->institution) }}" placeholder="e.g. XYZ Secondary School or Ilala Municipal Council" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green">
          <div class="text-xs text-slate-500">User's originating school or institution (e.g., Halmashauri).</div>
        </div>
      </div>

      <div>
        <button class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark">Save Changes</button>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
    <h2 class="font-semibold mb-4">Change Password</h2>
    <form action="{{ route('settings.password.update') }}" method="post" class="grid gap-4" data-show-loader>
      @csrf
      <div class="grid gap-1">
        <label class="text-sm text-slate-600">Current Password</label>
        <input type="password" name="current_password" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required>
      </div>
      <div class="grid gap-1">
        <label class="text-sm text-slate-600">New Password</label>
        <input type="password" name="password" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required>
      </div>
      <div class="grid gap-1">
        <label class="text-sm text-slate-600">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required>
      </div>
      <div>
        <button class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark">Update Password</button>
      </div>
    </form>
  </div>
</div>
@endsection
