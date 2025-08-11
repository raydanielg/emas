@extends('layouts.headmaster')

@section('title','Institution Contact Support | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Contact Support</h1>
    <p class="text-slate-500 mt-1">Send a message to technical/admin support about your institution. Attach screenshots, images, or audio if helpful.</p>
  </div>

  <div class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
    <form action="{{ route('support.send') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf
      
      @if(($schools ?? collect())->count())
        <div>
          <label for="school_id" class="block text-sm font-medium text-slate-700">School</label>
          <select id="school_id" name="school_id" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green">
            @foreach($schools as $s)
              <option value="{{ $s->id }}" data-code="{{ $s->code ?? '' }}">{{ $s->name }} @if(!empty($s->code))<span>({{ $s->code }})</span>@endif</option>
            @endforeach
          </select>
          <p class="mt-1 text-xs text-slate-500">If multiple schools are assigned, pick the one this request is about.</p>
        </div>
      @endif

      <div>
        <label for="message" class="block text-sm font-medium text-slate-700">Message</label>
        <textarea id="message" name="message" rows="5" class="mt-1 w-full rounded-md border-slate-300 focus:border-emas-green focus:ring-emas-green" placeholder="Describe the issue or request..."></textarea>
        <p class="mt-1 text-xs text-slate-500">Your message will be sent to support. We include your selected school for context.</p>
      </div>

      <div>
        <label for="attachment" class="block text-sm font-medium text-slate-700">Attachment (optional)</label>
        <input type="file" id="attachment" name="attachment" accept="image/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.csv" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emas-green/10 file:text-emas-green hover:file:bg-emas-green/20" />
        <p class="mt-1 text-xs text-slate-500">Max 10MB.</p>
      </div>

      <input type="hidden" id="_prefixed_message" name="_prefixed_message" value="" />

      <div class="pt-2">
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-emas-green text-white hover:bg-emerald-600">
          <i class="fa-solid fa-paper-plane"></i>
          <span>Send to Support</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Prefix message with institution context before submit without changing backend
  (function(){
    const form = document.currentScript.closest('div').querySelector('form');
    if (!form) return;
    form.addEventListener('submit', function(ev){
      const schoolSel = form.querySelector('#school_id');
      const msg = form.querySelector('#message');
      if (!msg) return;
      let prefix = '[INSTITUTION] ';
      if (schoolSel) {
        const opt = schoolSel.options[schoolSel.selectedIndex];
        const code = opt ? (opt.getAttribute('data-code') || '').trim() : '';
        const name = opt ? opt.textContent.trim() : '';
        if (name) prefix += name;
        if (code) prefix += ' ('+code+')';
        prefix += ': ';
      }
      if (msg.value && !msg.value.startsWith('[INSTITUTION]')) {
        msg.value = prefix + msg.value;
      }
    });
  })();
</script>
@endsection
