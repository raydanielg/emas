@extends('layouts.user')

@section('title', 'Technologies | eMAS')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Technologies</h1>
    <div class="flex items-center gap-2">
      <button id="exportBtn" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Export
      </button>
      <a href="#lead" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emas-green text-white hover:bg-emas-green/90 text-sm">
        Generate sales leads
      </a>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-6">
    <!-- Security -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">Security</h2>
        <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="space-y-2 text-sm">
        <li class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="font-medium">HSTS</span>
          </div>
          <span class="text-slate-500">Enabled</span>
        </li>
      </ul>
    </section>

    <!-- Fonts & Icons -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">Font scripts</h2>
        <a href="https://fonts.google.com/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="space-y-2 text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">Google Font API</span><span class="text-slate-500">Active</span></li>
        <li class="flex items-center justify-between"><span class="font-medium">Font Awesome</span><span class="text-slate-500">Active</span></li>
      </ul>
    </section>

    <!-- Web server -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">Web servers</h2>
        <a href="https://httpd.apache.org/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">Apache HTTP Server</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">2.x</span></li>
      </ul>
    </section>

    <!-- Charts -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">JavaScript graphics</h2>
        <a href="https://www.chartjs.org/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">Chart.js</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">4.x</span></li>
      </ul>
    </section>

    <!-- Languages -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">Programming languages</h2>
        <a href="https://www.php.net/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">PHP</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">8.x</span></li>
      </ul>
    </section>

    <!-- CDN -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">CDN</h2>
        <a href="https://unpkg.com/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">Unpkg</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">CDN</span></li>
      </ul>
    </section>

    <!-- JS Libraries -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">JavaScript libraries</h2>
        <a href="https://sweetalert.js.org/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="space-y-2 text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">SweetAlert</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">2.x</span></li>
        <li class="flex items-center justify-between"><span class="font-medium">Select2</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">4.x</span></li>
        <li class="flex items-center justify-between"><span class="font-medium">jQuery</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">3.6.0</span></li>
      </ul>
    </section>

    <!-- UI Frameworks -->
    <section class="bg-white rounded-xl ring-1 ring-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">UI frameworks</h2>
        <a href="https://getbootstrap.com/docs/4.6/getting-started/introduction/" target="_blank" class="text-emas-green text-sm hover:underline">More info</a>
      </div>
      <ul class="text-sm">
        <li class="flex items-center justify-between"><span class="font-medium">Bootstrap</span><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">4.6.0</span></li>
      </ul>
    </section>
  </div>

  <!-- CTA -->
  <div id="lead" class="mt-8 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <div class="text-sm uppercase tracking-wide text-emerald-700 font-semibold">Something wrong or missing?</div>
      <div class="text-lg font-semibold text-emerald-900">Generate sales leads</div>
      <p class="text-sm text-emerald-800 mt-1">Find new prospects by the technologies they use. Reach out to customers of Shopify, Magento, Salesforce and others.</p>
    </div>
    <div class="flex items-center gap-2">
      <button id="copySummary" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-50 text-sm">
        Copy summary
      </button>
      <a href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emas-green text-white hover:bg-emas-green/90 text-sm">Create a lead list</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const exportBtn = document.getElementById('exportBtn');
  const copyBtn = document.getElementById('copySummary');
  function buildSummary() {
    return `Security: HSTS\nFont scripts: Google Font API, Font Awesome\nWeb servers: Apache HTTP Server\nJavaScript graphics: Chart.js\nProgramming languages: PHP\nCDN: Unpkg\nJavaScript libraries: SweetAlert, Select2, jQuery 3.6.0\nUI frameworks: Bootstrap 4.6.0`;
  }
  exportBtn?.addEventListener('click', () => {
    const data = buildSummary();
    const blob = new Blob([data], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'technologies.txt'; a.click();
    URL.revokeObjectURL(url);
  });
  copyBtn?.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(buildSummary()); copyBtn.textContent='Copied!'; setTimeout(()=>copyBtn.textContent='Copy summary',1500);} catch(e) {}
  });
</script>
@endpush
