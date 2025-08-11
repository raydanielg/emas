<nav class="space-y-1 text-sm">
  <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 font-semibold' : '' }}">
    <i class="fa-solid fa-gauge"></i>
    <span>Dashboard</span>
  </a>
  <a href="#" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100">
    <i class="fa-solid fa-users"></i>
    <span>Users</span>
  </a>
  <a href="#" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100">
    <i class="fa-solid fa-school"></i>
    <span>Institutions</span>
  </a>
  <a href="#" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100">
    <i class="fa-solid fa-gear"></i>
    <span>Settings</span>
  </a>
</nav>
