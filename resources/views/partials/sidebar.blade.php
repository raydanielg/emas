<nav class="flex-1 p-3 text-slate-800">
  <ul class="space-y-1">
    <li>
      @php $active = request()->routeIs('dashboard'); @endphp
      <a href="/dashboard" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-gauge h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('marking.students') || request()->routeIs('marking.students.show'); @endphp
      <a href="/marking/students" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-user-graduate h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Students</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('marking.centres') || request()->routeIs('marking.centres.sheet'); @endphp
      <a href="/marking/centres" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-school h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Institutions</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('reports.*'); @endphp
      <a href="{{ route('reports.progress') }}" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-chart-line h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Reports</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('marking.index') || request()->is('marking/ca'); @endphp
      <a href="/marking" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-pen-to-square h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Marking</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('support.*'); @endphp
      <a href="{{ route('support.index') }}" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-headset h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Support</span>
      </a>
    </li>
    <li>
      @php $active = request()->routeIs('settings.*'); @endphp
      <a href="{{ route('settings.profile') }}" class="show-loader group flex items-center gap-3 px-3 py-2 rounded-md border-l-4 {{ $active ? 'bg-slate-200 text-slate-900 border-emerald-600' : 'text-slate-700 border-transparent hover:bg-slate-200 hover:text-slate-900 hover:border-emerald-600' }}">
        <i class="fa-solid fa-gear h-5 w-5 {{ $active ? 'text-emerald-700' : 'text-slate-600 group-hover:text-emerald-700' }}"></i>
        <span>Settings</span>
      </a>
    </li>

    
  </ul>
</nav>
