<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'eMAS | User')</title>
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

    /* Scaled sizes for ripple to avoid re-authoring keyframes */
    .lds-sm { transform: scale(0.35); transform-origin: center; }
    .lds-xs { transform: scale(0.25); transform-origin: center; }

    /* Full-page overlay helper */
    .page-loading-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(255,255,255,0.75); backdrop-filter: blur(1px); z-index: 60; }
    .page-loading-overlay.show { display: flex; }

    /* Ring loader (green) */
    .lds-ring, .lds-ring div { box-sizing: border-box; }
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; color: #1EB53A; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid currentColor; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: currentColor transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Number loading helper: toggle via data-loading="true" */
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
      </div>
      <div class="h-px bg-gray-200"></div>
      @include('partials.sidebar')
      <div class="p-4 border-t text-sm text-slate-500">© <span class="font-semibold">eMAS</span></div>
    </aside>

    <!-- Mobile Sidebar + Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black/40 z-40 hidden"></div>
    <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-gray-100 text-slate-800 ring-1 ring-gray-300 transform -translate-x-full transition-transform duration-200 ease-in-out md:hidden flex flex-col">
      <div class="h-16 flex items-center justify-between gap-2 px-5 bg-emas-green text-white">
        <div class="flex items-center gap-2">
          <span class="font-extrabold tracking-wide">eMAS</span>
        </div>
        <button id="closeSidebar" class="p-2 rounded-lg hover:bg-emerald-700/30" aria-label="Close menu">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="h-px bg-gray-200"></div>
      @include('partials.sidebar')
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <header class="h-16 bg-emas-green text-white px-3 lg:px-4 flex items-center justify-between sticky top-0 z-40 shadow">
        <div class="flex items-center gap-3 md:hidden">
          <button id="openSidebar" class="p-2 rounded-lg hover:bg-slate-100" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
          <span class="font-bold">eMAS</span>
        </div>
        <div class="hidden md:flex items-center gap-2 flex-1 max-w-[920px] px-2">
          @hasSection('top_filters')
            @yield('top_filters')
          @else
            @include('partials.top-filters')
          @endif
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <button id="notifBtn" class="relative rounded-full h-9 w-9 flex items-center justify-center bg-white/10 text-white hover:bg-white/20 ring-1 ring-white/20" title="Notifications">
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a6 6 0 016 6v3.586l1.707 1.707A1 1 0 0119.293 15H4.707a1 1 0 01-.707-1.707L5.707 11.586V8a6 6 0 016-6zm0 20a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
              <span id="notifBadge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1.5 rounded-full bg-emas-yellow text-slate-900 text-[11px] leading-[18px] font-bold text-center"></span>
            </button>
            <div id="notifMenu" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-slate-200 hidden">
              <div class="px-3 py-2 border-b text-sm font-semibold text-slate-700">Notifications</div>
              <div id="notifList" class="max-h-80 overflow-auto divide-y"></div>
              <div class="px-3 py-2 text-sm text-right">
                <a href="{{ route('notifications.index') }}" class="text-emas-green hover:underline">View all</a>
              </div>
            </div>
          </div>
          <div class="relative">
            <button id="userMenuBtn" class="flex items-center gap-2 pl-1 pr-2 py-1.5 rounded-full hover:bg-white/10">
              @php $u = Auth::user(); $initials = $u ? collect(explode(' ', trim($u->name ?? 'U')))->map(fn($p)=>mb_substr($p,0,1))->join('') : 'U'; @endphp
              @if ($u && $u->avatar_path)
                <img src="{{ asset('storage/'.$u->avatar_path) }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover ring-1 ring-white/30">
              @else
                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 text-white font-bold flex items-center justify-center">{{ $initials }}</div>
              @endif
              <div class="hidden sm:flex flex-col items-start leading-tight">
                <span class="text-sm font-semibold text-white">{{ $u->name ?? 'User' }}</span>
                <span class="text-xs text-white/80">Signed in</span>
              </div>
              <i class="fa-solid fa-chevron-down text-white/80 text-xs"></i>
            </button>
            <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-slate-200 hidden">
              <a href="{{ route('settings.profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-user-gear text-slate-500"></i> Profile & Settings
              </a>
              <a href="{{ route('settings.profile') }}#password" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-key text-slate-500"></i> Change Password
              </a>
              <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                  <i class="fa-solid fa-right-from-bracket"></i> Logout
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
    // Mobile sidebar toggle
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const panel = document.getElementById('mobileSidebar');
    function openSide() {
      panel.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
    function closeSide() {
      panel.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
    openBtn && openBtn.addEventListener('click', openSide);
    closeBtn && closeBtn.addEventListener('click', closeSide);
    overlay && overlay.addEventListener('click', closeSide);
    // Close on escape
    window.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeSide(); });

    // Page loading overlay helpers
    const pageOverlay = document.getElementById('pageLoadingOverlay');
    function showPageLoader(){ pageOverlay && pageOverlay.classList.add('show'); }
    function hidePageLoader(){ pageOverlay && pageOverlay.classList.remove('show'); }
    // Auto show on any <form data-show-loader> submission
    document.addEventListener('submit', (e)=>{
      const f = e.target;
      if (f && f.matches('form[data-show-loader]')) { showPageLoader(); }
    }, true);
    // Auto show on any link with .show-loader
    document.addEventListener('click', (e)=>{
      const a = e.target.closest('a.show-loader');
      if (a && a.getAttribute('href') && !a.getAttribute('target')) {
        showPageLoader();
      }
    });

    // User menu dropdown toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenu = document.getElementById('userMenu');
    function hideUserMenu(){ userMenu && userMenu.classList.add('hidden'); }
    function toggleUserMenu(){ userMenu && userMenu.classList.toggle('hidden'); }
    userMenuBtn && userMenuBtn.addEventListener('click', (e)=>{ e.stopPropagation(); toggleUserMenu(); });
    document.addEventListener('click', (e)=>{
      if (userMenu && !e.target.closest('#userMenu')) hideUserMenu();
    });

    // Notifications preview dropdown
    const notifBtn = document.getElementById('notifBtn');
    const notifMenu = document.getElementById('notifMenu');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');
    function toggleNotif(){ notifMenu && notifMenu.classList.toggle('hidden'); }
    function hideNotif(){ notifMenu && notifMenu.classList.add('hidden'); }
    notifBtn && notifBtn.addEventListener('click', (e)=>{ e.stopPropagation(); toggleNotif(); });
    document.addEventListener('click', (e)=>{ if(notifMenu && !e.target.closest('#notifMenu')) hideNotif(); });

    // Helper to toggle number loading spinners
    window.toggleNumberLoading = function(selectorOrEl, loading) {
      const el = (typeof selectorOrEl === 'string') ? document.querySelector(selectorOrEl) : selectorOrEl;
      if (!el) return;
      if (loading) el.setAttribute('data-loading','true'); else el.setAttribute('data-loading','false');
    }

    async function loadLatestNotifs(){
      try {
        const res = await fetch('{{ route('notifications.latest') }}', { headers: {'X-Requested-With':'XMLHttpRequest'} });
        const data = await res.json();
        const unread = data.unread ?? 0;
        if (unread > 0) {
          notifBadge.textContent = unread;
          notifBadge.classList.remove('hidden');
        } else {
          notifBadge.classList.add('hidden');
        }
        notifList.innerHTML = '';
        (data.items || []).forEach(item => {
          const a = document.createElement('a');
          a.href = '{{ url('/notifications') }}/' + item.id;
          a.className = 'block px-3 py-2 hover:bg-slate-50';
          const title = document.createElement('div');
          title.className = 'text-sm font-medium ' + (item.read_at ? 'text-slate-700' : 'text-slate-900');
          title.textContent = item.title;
          const meta = document.createElement('div');
          meta.className = 'text-xs text-slate-500';
          meta.textContent = new Date(item.created_at).toLocaleString();
          a.appendChild(title); a.appendChild(meta);
          notifList.appendChild(a);
        });
        if ((data.items || []).length === 0) {
          const d = document.createElement('div');
          d.className = 'px-3 py-4 text-sm text-slate-500';
          d.textContent = 'No notifications';
          notifList.appendChild(d);
        }
      } catch (e) { /* ignore */ }
    }
    loadLatestNotifs();
    setInterval(loadLatestNotifs, 60000);
  </script>
  <!-- Global page loading overlay -->
  <div id="pageLoadingOverlay" class="page-loading-overlay">
    <div class="lds-ripple"><div></div><div></div></div>
  </div>
</body>
</html>
