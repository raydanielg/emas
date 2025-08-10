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
        /* Loader in green */
        .loader { width: 48px; height: 48px; display: inline-block; position: relative; }
        .loader::after, .loader::before { content: ''; box-sizing: border-box; width: 48px; height: 48px; border-radius: 50%; border: 2px solid #1EB53A; position: absolute; left: 0; top: 0; animation: animloader 2s linear infinite; }
        .loader::after { animation-delay: 1s; }
        @keyframes animloader { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(1); opacity: 0; } }
    </style>

    @stack('head')
</head>
<body class="min-h-screen bg-emerald-50/30 relative">

<!-- Loader overlay -->
<div id="page-loader" class="fixed inset-0 z-50 hidden items-center justify-center bg-white/70 backdrop-blur-sm">
  <span class="loader"></span>
</div>

<!-- Background image and blur overlay -->
<div class="fixed inset-0 -z-10">
  <img src="/images/exam-bg.svg" alt="Exam background" class="w-full h-full object-cover opacity-80">
  <div class="absolute inset-0 bg-white/40 backdrop-blur-sm"></div>
</div>

<!-- Page container -->
<div class="min-h-screen flex items-center justify-center px-4 py-10">
  @yield('content')
</div>

<script>
  // Hide loader after load
  window.addEventListener('load', function(){
    const l = document.getElementById('page-loader');
    if (l) l.classList.add('hidden');
  });
  // Show on form submit
  document.addEventListener('submit', function(){
    const l = document.getElementById('page-loader');
    if (l) l.classList.remove('hidden');
  }, true);
  // Manual API
  window.toggleLoader = function(show){
    const l = document.getElementById('page-loader');
    if (!l) return; l.classList[show ? 'remove' : 'add']('hidden');
  }
</script>
@stack('scripts')
</body>
</html>
