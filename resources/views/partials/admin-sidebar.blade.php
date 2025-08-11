<nav class="space-y-1 text-sm">
  <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-50 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">
    <i class="fa-solid fa-gauge"></i>
    <span>Dashboard</span>
  </a>
  <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-50 {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">
    <i class="fa-solid fa-users"></i>
    <span>Users</span>
  </a>
  <a href="{{ route('admin.institutions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-50 {{ request()->routeIs('admin.institutions.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">
    <i class="fa-solid fa-school"></i>
    <span>Institutions</span>
  </a>
  <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-50 {{ request()->routeIs('admin.analytics.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">
    <i class="fa-solid fa-chart-pie"></i>
    <span>Analytics</span>
  </a>
  <a href="#" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-50">
    <i class="fa-solid fa-gear"></i>
    <span>Settings</span>
  </a>
</nav>
