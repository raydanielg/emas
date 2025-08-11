@extends('layouts.user')

@section('title','Support | eMAS')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-xl font-semibold mb-4">Support</h1>

  <div class="rounded-xl ring-1 ring-gray-200 overflow-hidden">
    <!-- Chat window with patterned background -->
    <div id="chatWindow" class="max-h-[70vh] overflow-y-auto p-4 space-y-3 bg-white relative">
      <div class="absolute inset-0 -z-10 opacity-20" style="background-image:url('/images/pattern-randomized.svg'); background-size: 320px; background-repeat: repeat;"></div>

      @forelse($messages as $m)
        @php
          $isMe = $m->role === 'user';
          $bubble = $isMe ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800';
          $align = $isMe ? 'justify-end' : 'justify-start';
          $media = $m->attachment_type ?? null;
          $path = $m->attachment_path ?? null;
          $url = $path ? (str_starts_with($path,'support/') ? asset('storage/'.$path) : asset($path)) : null;
        @endphp
        <div class="flex {{ $align }}">
          <div class="max-w-[78%] px-3 py-2 rounded-2xl text-sm {{ $bubble }} shadow-sm">
            @if(!empty($m->message))
              <div class="whitespace-pre-line">{{ $m->message }}</div>
            @endif

            @if($url)
              <div class="mt-2">
                @if($media === 'image')
                  <a href="{{ $url }}" target="_blank" class="block">
                    <img src="{{ $url }}" alt="image" class="rounded-lg max-h-64 object-contain bg-white">
                  </a>
                @elseif($media === 'audio')
                  <audio controls class="w-64">
                    <source src="{{ $url }}">
                    Your browser does not support the audio element.
                  </audio>
                @else
                  <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-2 underline">
                    <i class="fa-solid fa-paperclip"></i>
                    <span>{{ $m->attachment_name ?? 'Download file' }}</span>
                  </a>
                @endif
              </div>
            @endif

            <div class="mt-1 text-[11px] opacity-75">{{ \Carbon\Carbon::parse($m->created_at)->diffForHumans() }}</div>
          </div>
        </div>
      @empty
        <div class="text-center text-slate-500 text-sm">No messages yet. Say hello 👋</div>
      @endforelse
    </div>

    <!-- Input bar -->
    <form id="chatForm" action="{{ route('support.send') }}" method="post" enctype="multipart/form-data" class="bg-white border-t p-3 flex items-center gap-2" data-show-loader>
      @csrf
      <label class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full border hover:bg-slate-50 cursor-pointer" title="Attach">
        <i class="fa-solid fa-paperclip"></i>
        <input type="file" name="attachment" class="hidden" />
      </label>
      <button id="recBtn" type="button" class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full border hover:bg-slate-50" title="Record voice">
        <i class="fa-solid fa-microphone"></i>
      </button>
      <input type="text" name="message" value="{{ old('message') }}" placeholder="Type your message" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emas-green" maxlength="2000" />
      <button class="px-4 py-2 bg-emas-green text-white rounded-lg hover:bg-emas-greenDark">Send</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const chat = document.getElementById('chatWindow');
  if (chat) { chat.scrollTop = chat.scrollHeight; }

  // Voice recorder using MediaRecorder, posts blob as 'voice'
  const recBtn = document.getElementById('recBtn');
  const chatForm = document.getElementById('chatForm');
  let mediaRecorder; let chunks = []; let recording = false;
  if (recBtn && navigator.mediaDevices && window.MediaRecorder) {
    recBtn.addEventListener('click', async () => {
      try {
        if (!recording) {
          const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
          mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
          chunks = [];
          mediaRecorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };
          mediaRecorder.onstop = async () => {
            const blob = new Blob(chunks, { type: 'audio/webm' });
            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            fd.append('voice', blob, 'voice.webm');
            // Optional: include current text if any
            const msg = chatForm.querySelector('input[name="message"]').value;
            if (msg) fd.append('message', msg);
            await fetch("{{ route('support.send') }}", { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
            window.location.reload();
          };
          mediaRecorder.start();
          recording = true;
          recBtn.classList.add('text-emas-green');
        } else {
          mediaRecorder.stop();
          recording = false;
          recBtn.classList.remove('text-emas-green');
        }
      } catch (e) {
        alert('Microphone access denied or not supported.');
        console.error(e);
      }
    });
  }
</script>
@endpush
