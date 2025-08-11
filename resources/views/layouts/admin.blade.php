<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title','Admin Panel')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-NWj4b6n7oQx7KqkJr7bQeKQ0r3qkA7l9xD1u2y1m7zq9s0Q3m2s9sK9C7bQF7o9f+zj9G8Z8tS9o4zQ2x2g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-slate-50">
  <div class="w-full h-1 bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700"></div>
  <div class="min-h-screen grid grid-cols-12">
    <!-- Sidebar -->
    <aside class="col-span-12 md:col-span-3 lg:col-span-2 bg-white border-r border-slate-200 p-3">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded bg-emerald-100 grid place-items-center"><i class="fa-solid fa-shield-halved text-emerald-700"></i></div>
        <span class="font-semibold">Admin Console</span>
      </div>
      @include('partials.admin-sidebar')
    </aside>

    <!-- Main -->
    <main class="col-span-12 md:col-span-9 lg:col-span-10">
      <!-- Header -->
      <header class="bg-white/90 backdrop-blur border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-3">
          <button class="md:hidden inline-flex items-center px-2 py-1 rounded border">Menu</button>
          <h1 class="text-lg font-semibold text-slate-800">@yield('page_title','Dashboard')</h1>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-600">{{ Auth::user()->name ?? 'Admin' }}</span>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="px-3 py-1 rounded bg-emerald-600 text-white hover:bg-emerald-700">Logout</button>
          </form>
        </div>
      </header>

      <!-- Content -->
      <section class="p-4">
        @yield('content')
      </section>
    </main>
  </div>
</body>
</html>
