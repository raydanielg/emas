<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'eMAS | Headmaster')</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
  <link rel="alternate icon" href="/favicon.ico" />

  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            emas: {
              green: '#1EB53A',
              greenDark: '#14882A',
              yellow: '#FCD116',
              dark: '#0b3d1b'
            }
          }
        }
      }
    }
  </script>

  <!-- Chart.js for simple bar charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer" />
  @stack('head')
  <style>
    /* Simple scrollbar styling */
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }

    /* Ripple loader */
    .lds-ripple,
    .lds-ripple div { box-sizing: border-box; }
    .lds-ripple { display: inline-block; position: relative; width: 80px; height: 80px; color: #1EB53A; }
    .lds-ripple div { position: absolute; border: 4px solid currentColor; opacity: 1; border-radius: 50%; animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; }
    .lds-ripple div:nth-child(2) { animation-delay: -0.5s; }
    @keyframes lds-ripple {
      0% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 0; }
      4.9% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 0; }
      5% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 1; }
      100% { top: 0; left: 0; width: 80px; height: 80px; opacity: 0; }
    }

    .lds-sm { transform: scale(0.35); transform-origin: center; }
    .lds-xs { transform: scale(0.25); transform-origin: center; }

    .page-loading-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(255,255,255,0.75); backdrop-filter: blur(1px); z-index: 60; }
    .page-loading-overlay.show { display: flex; }

    /* Ring loader */
    .lds-ring, .lds-ring div { box-sizing: border-box; }
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; color: #FCD116; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid currentColor; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: currentColor transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .num-loading .spinner { display: none; }
    .num-loading[data-loading="true"] .spinner { display: inline-flex; }
    .num-loading[data-loading="true"] .number { display: none; }
  </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
  <div class="flex min-h-screen">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:w-64 lg:w-72 flex-col bg-gray-100 text-slate-800 ring-1 ring-gray-300">
      <div class="h-16 flex items-center px-5 bg-emas-green text-white">
        <span class="font-extrabold tracking-wide">eMAS</span>
        <span class="ml-2 text-white/80 text-sm">Headmaster</span>
      </div>
      <div class="h-px bg-gray-200"></div>
      @include('partials.headmaster-sidebar')
      <div class="mt-auto"></div>
      <div class="p-4 border-t text-sm text-slate-500">© <span class="font-semibold">eMAS</span></div>
    </aside>

    <!-- Mobile Sidebar + Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black/40 z-40 hidden"></div>
    <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-gray-100 text-slate-800 ring-1 ring-gray-300 transform -translate-x-full transition-transform duration-200 ease-in-out md:hidden flex flex-col">
      <div class="h-16 flex items-center justify-between px-4 bg-emas-green text-white">
        <div class="font-extrabold">eMAS <span class="text-white/80 text-sm">Headmaster</span></div>
        <button id="closeMobile" class="p-2 hover:bg-white/10 rounded">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="h-px bg-gray-200"></div>
      @include('partials.headmaster-sidebar')
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <header class="h-16 bg-emas-green text-white px-3 lg:px-4 flex items-center justify-between sticky top-0 z-40 shadow">
        <div class="flex items-center gap-3 md:hidden">
          <button id="openMobile" class="p-2 hover:bg-white/10 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
          <span class="font-semibold">Headmaster</span>
        </div>
        <div class="hidden md:flex items-center gap-2 flex-1 max-w-[920px] px-2">
          <div class="text-sm text-white/80">Welcome, {{ auth()->user()->name ?? 'User' }}</div>
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ route('support.index') }}" class="relative p-2 rounded hover:bg-white/10" title="Support">
            <i class="fa-solid fa-headset"></i>
          </a>
          <div class="relative">
            <button id="userMenuBtn" class="flex items-center gap-2 pl-1 pr-2 py-1.5 rounded-full hover:bg-white/10">
              @php $u = auth()->user(); @endphp
              @if (($u->avatar_path ?? null))
                @php $src = str_starts_with($u->avatar_path,'profiles/') ? asset('storage/'.$u->avatar_path) : asset($u->avatar_path); @endphp
                <img src="{{ $src }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover ring-1 ring-white/30">
              @else
                <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center">
                  <span class="text-sm font-semibold">{{ strtoupper(substr($u->name ?? 'U',0,1)) }}</span>
                </div>
              @endif
              <div class="hidden md:block text-left">
                <div class="text-sm font-semibold">{{ $u->name ?? 'User' }}</div>
                <span class="text-xs text-white/80">Headmaster</span>
              </div>
              <i class="fa-solid fa-chevron-down text-white/80 text-xs"></i>
            </button>
            <div id="userMenu" class="absolute right-0 mt-2 w-52 bg-white text-slate-800 rounded-md shadow-xl ring-1 ring-slate-200 overflow-hidden hidden">
              <a href="{{ route('headmaster.profile') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-100">
                <i class="fa-regular fa-user text-slate-600"></i>
                <span>My Profile</span>
              </a>
              <div class="h-px bg-slate-200"></div>
              <form action="{{ route('logout') }}" method="post" class="m-0">
                @csrf
                <button class="w-full flex items-center gap-2 px-3 py-2 text-left hover:bg-red-50 text-red-600">
                  <i class="fa-solid fa-right-from-bracket"></i>
                  <span>Logout</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-4 lg:p-6">
        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
  <script>
    // Mobile sidebar toggles
    const openBtn = document.getElementById('openMobile');
    const closeBtn = document.getElementById('closeMobile');
    const mobile = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileOverlay');
    function openSide(){ mobile && mobile.classList.remove('-translate-x-full'); overlay && (overlay.style.display='block'); }
    function closeSide(){ mobile && mobile.classList.add('-translate-x-full'); overlay && (overlay.style.display='none'); }
    openBtn && openBtn.addEventListener('click', openSide);
    closeBtn && closeBtn.addEventListener('click', closeSide);
    overlay && overlay.addEventListener('click', closeSide);

    // Page loading overlay helpers
    const pageOverlay = document.getElementById('pageLoadingOverlay');
    function showPageLoader(){ pageOverlay && pageOverlay.classList.add('show'); }
    function hidePageLoader(){ pageOverlay && pageOverlay.classList.remove('show'); }
    document.addEventListener('submit', (e)=>{
      const f = e.target;
      if (f && f.matches('form[data-show-loader]')) { showPageLoader(); }
    }, true);
    document.addEventListener('click', (e)=>{
      const a = e.target.closest('a.show-loader');
      if (a && a.getAttribute('href') && !a.getAttribute('target')) { showPageLoader(); }
    });

    // User menu dropdown toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenu = document.getElementById('userMenu');
    function hideUserMenu(){ userMenu && userMenu.classList.add('hidden'); }
    function toggleUserMenu(){ userMenu && userMenu.classList.toggle('hidden'); }
    userMenuBtn && userMenuBtn.addEventListener('click', (e)=>{ e.stopPropagation(); toggleUserMenu(); });
    document.addEventListener('click', (e)=>{ if (userMenu && !e.target.closest('#userMenu')) hideUserMenu(); });
  </script>
  <!-- Global page loading overlay -->
  <div id="pageLoadingOverlay" class="page-loading-overlay">
    <div class="lds-ripple"><div></div><div></div></div>
  </div>
</body>
</html>
