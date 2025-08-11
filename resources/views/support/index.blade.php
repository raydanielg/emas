@extends('layouts.user')

@section('title','Support | eMAS')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-xl font-semibold mb-4">Support</h1>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 overflow-hidden">
    <div id="chatWindow" class="max-h-[60vh] overflow-y-auto p-4 space-y-3">
      @forelse($messages as $m)
        <div class="flex {{ $m->role === 'user' ? 'justify-end' : 'justify-start' }}">
          <div class="max-w-[75%] px-3 py-2 rounded-lg text-sm {{ $m->role === 'user' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800' }}">
            <div>{{ $m->message }}</div>
            <div class="mt-1 text-xs opacity-70">{{ \Carbon\Carbon::parse($m->created_at)->diffForHumans() }}</div>
          </div>
        </div>
      @empty
        <div class="text-center text-slate-500 text-sm">No messages yet. Say hello 👋</div>
      @endforelse
    </div>
    <form action="{{ route('support.send') }}" method="post" class="border-t p-3 flex gap-2" data-show-loader>
      @csrf
      <input type="text" name="message" value="{{ old('message') }}" placeholder="Type your message..." class="flex-1 rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" required maxlength="2000" />
      <button class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark">Send</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const chat = document.getElementById('chatWindow');
  if (chat) { chat.scrollTop = chat.scrollHeight; }
</script>
@endpush
