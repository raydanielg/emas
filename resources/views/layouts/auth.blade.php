<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EMAS')</title>
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

    <style>
        /* Ripple loader (uses currentColor). Set parent to text-emas-green */
        .lds-ripple,
        .lds-ripple div { box-sizing: border-box; }
        .lds-ripple { display: inline-block; position: relative; width: 80px; height: 80px; }
        .lds-ripple div {
          position: absolute; border: 4px solid currentColor; opacity: 1; border-radius: 50%;
          animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }
        .lds-ripple div:nth-child(2) { animation-delay: -0.5s; }
        @keyframes lds-ripple {
          0% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 0; }
          4.9% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 0; }
          5% { top: 36px; left: 36px; width: 8px; height: 8px; opacity: 1; }
          100% { top: 0; left: 0; width: 80px; height: 80px; opacity: 0; }
        }

        /* Inline button loader (facebook style) uses currentColor */
        .lds-facebook,
        .lds-facebook div { box-sizing: border-box; }
        .lds-facebook { display: inline-block; position: relative; width: 80px; height: 80px; }
        .lds-facebook div {
          display: inline-block; position: absolute; left: 8px; width: 16px; background: currentColor;
          animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        }
        .lds-facebook div:nth-child(1) { left: 8px; animation-delay: -0.24s; }
        .lds-facebook div:nth-child(2) { left: 32px; animation-delay: -0.12s; }
        .lds-facebook div:nth-child(3) { left: 56px; animation-delay: 0s; }
        @keyframes lds-facebook {
          0% { top: 8px; height: 64px; }
          50%, 100% { top: 24px; height: 32px; }
        }
    </style>

    @stack('head')
</head>
<body class="min-h-screen bg-emerald-50/30 relative">

<!-- Loader overlay -->
<div id="page-loader" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-white/70 backdrop-blur-sm">
  <div class="lds-ripple text-emas-green"><div></div><div></div></div>
  </div>

<!-- Background image and blur overlay -->
<div class="fixed inset-0 -z-10">
  <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1920&q=60" alt="Background" class="w-full h-full object-cover opacity-80 blur-md scale-105">
  <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
</div>

<!-- Page container -->
<div class="min-h-screen flex items-center justify-center px-4 py-10">
  @yield('content')
</div>

<script>
  // Loader helpers
  const loaderEl = document.getElementById('page-loader');
  const showLoader = () => loaderEl && loaderEl.classList.remove('hidden');
  const hideLoader = () => loaderEl && loaderEl.classList.add('hidden');

  // Hide loader after initial page load
  window.addEventListener('load', hideLoader);
  // Show loader on form submits (navigations)
  document.addEventListener('submit', showLoader, true);
  // Manual API
  window.toggleLoader = (show) => (show ? showLoader() : hideLoader());

  // Network activity tracking (fetch + XHR)
  (function(){
    let pending = 0; const inc = ()=>{ pending++; showLoader(); }; const dec = ()=>{ pending=Math.max(0,pending-1); if(!pending) hideLoader(); };
    // Patch fetch
    const origFetch = window.fetch; if (origFetch) {
      window.fetch = function(...args){ inc(); return origFetch.apply(this, args).finally(dec); };
    }
    // Patch XHR
    const OrigXHR = window.XMLHttpRequest; if (OrigXHR) {
      const P = OrigXHR.prototype; const open = P.open; const send = P.send;
      P.open = function(...a){ this.__track = true; return open.apply(this, a); };
      P.send = function(...a){ if (this.__track) { inc(); this.addEventListener('loadend', dec, { once:true }); } return send.apply(this, a); };
    }
  })();
</script>
@stack('scripts')
</body>
</html>
