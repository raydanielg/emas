@extends('layouts.headmaster')

@section('title','Settings | Headmaster')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">System Preferences</h1>

  @if(session('status'))
    <div class="mb-3 p-3 rounded bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-3 p-3 rounded bg-rose-50 text-rose-800 ring-1 ring-rose-200">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white rounded ring-1 ring-slate-200 p-4">
    <form method="POST" action="{{ route('headmaster.settings.save') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-slate-700">Language</label>
        <select name="locale" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" {{ $canEdit ? '' : 'disabled' }}>
          <option value="en" {{ ($prefs['locale'] ?? 'en')==='en' ? 'selected' : '' }}>English</option>
          <option value="sw" {{ ($prefs['locale'] ?? 'en')==='sw' ? 'selected' : '' }}>Kiswahili</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Theme</label>
        <select name="theme" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" {{ $canEdit ? '' : 'disabled' }}>
          <option value="light" {{ ($prefs['theme'] ?? 'light')==='light' ? 'selected' : '' }}>Light</option>
          <option value="dark" {{ ($prefs['theme'] ?? 'light')==='dark' ? 'selected' : '' }}>Dark</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Academic Year</label>
        <input type="text" name="term_year" value="{{ $prefs['term_year'] ?? '' }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" {{ $canEdit ? '' : 'disabled' }} />
      </div>
      <div class="md:col-span-2">
        <fieldset class="space-y-2">
          <legend class="text-sm font-medium text-slate-700">Notifications</legend>
          <label class="inline-flex items-center gap-2">
            <input type="hidden" name="notifications_email" value="0" />
            <input type="checkbox" name="notifications_email" value="1" {{ ($prefs['notifications_email'] ?? '0')==='1' ? 'checked' : '' }} {{ $canEdit ? '' : 'disabled' }} />
            <span>Email notifications</span>
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="hidden" name="notifications_sms" value="0" />
            <input type="checkbox" name="notifications_sms" value="1" {{ ($prefs['notifications_sms'] ?? '0')==='1' ? 'checked' : '' }} {{ $canEdit ? '' : 'disabled' }} />
            <span>SMS notifications</span>
          </label>
        </fieldset>
      </div>
      <div class="md:col-span-2 flex items-center gap-3">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-emas-green text-white hover:bg-emerald-600" {{ $canEdit ? '' : 'disabled' }}>
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Save Preferences</span>
        </button>
        @unless($canEdit)
        <span class="text-sm text-slate-500">Editing imefungwa. Tafadhali wasiliana na admin kupata ruhusa.</span>
        @endunless
      </div>
    </form>
  </div>
</div>
@endsection
