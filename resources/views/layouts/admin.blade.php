<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title','Admin Panel')</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
  <div class="min-h-screen grid grid-cols-12">
    <!-- Sidebar -->
    <aside class="col-span-12 md:col-span-3 lg:col-span-2 bg-white border-r border-slate-200 p-3">
      <div class="flex items-center gap-2 mb-4">
        <i class="fa-solid fa-shield-halved text-emas-green"></i>
        <span class="font-semibold">Admin</span>
      </div>
      @include('partials.admin-sidebar')
    </aside>

    <!-- Main -->
    <main class="col-span-12 md:col-span-9 lg:col-span-10">
      <!-- Header -->
      <header class="bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button class="md:hidden inline-flex items-center px-2 py-1 rounded border">Menu</button>
          <h1 class="text-lg font-semibold">@yield('page_title','Dashboard')</h1>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-600">{{ Auth::user()->name ?? 'Admin' }}</span>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="px-3 py-1 rounded bg-slate-800 text-white hover:bg-black">Logout</button>
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
