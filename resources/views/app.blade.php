<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title inertia>eMAS</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
  @vite(['resources/css/app.css','resources/js/app.js'])
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
