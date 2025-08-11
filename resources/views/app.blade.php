<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title inertia>eMAS</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Ripple loader (global, green) */
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
  </style>
</head>
<body class="min-h-screen bg-emerald-50/30 relative">
  <!-- Background -->
  <div class="fixed inset-0 -z-10">
    <img src="/images/exam-bg.svg" alt="Exam background" class="w-full h-full object-cover opacity-80">
    <div class="absolute inset-0 bg-white/40 backdrop-blur-sm"></div>
  </div>

  @inertia
</body>
</html>
