<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Emas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Global loader style provided by user */
        .loader {
          width: 48px;
          height: 48px;
          display: inline-block;
          position: relative;
        }
        .loader::after,
        .loader::before {
          content: '';
          box-sizing: border-box;
          width: 48px;
          height: 48px;
          border-radius: 50%;
          border: 2px solid #FFF;
          position: absolute;
          left: 0;
          top: 0;
          animation: animloader 2s linear infinite;
        }
        .loader::after { animation-delay: 1s; }
        @keyframes animloader {
          0% { transform: scale(0); opacity: 1; }
          100% { transform: scale(1); opacity: 0; }
        }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">

    <!-- Page Loader Overlay -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden items-center justify-center bg-white/80 backdrop-blur-sm">
        <span class="loader"></span>
    </div>

    @yield('body')

    <script>
        // Hide loader after first paint
        window.addEventListener('load', function () {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.add('hidden');
        });
        // Show loader on any form submission
        document.addEventListener('submit', function (e) {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.remove('hidden');
        }, true);
        // Utility to toggle loader manually if needed
        window.toggleLoader = function(show) {
            const loader = document.getElementById('page-loader');
            if (!loader) return;
            loader.classList[show ? 'remove' : 'add']('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
