<nav class="p-3 space-y-3 text-sm">
  {{-- Dashboard (single) --}}
  <div>
    <a href="{{ route('headmaster.dashboard') }}" class="mt-1 flex items-center justify-between px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.dashboard') ? 'bg-white font-semibold text-emas-green' : '' }}">
      <span class="flex items-center gap-2">
        <i class="fa-solid fa-gauge"></i>
        <span>Dashboard</span>
      </span>
    </a>
  </div>

  {{-- Students (dropdown) --}}
  @php $studentsOpen = request()->routeIs('headmaster.students.*'); @endphp
  <div>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-white menu-toggle" aria-expanded="{{ $studentsOpen ? 'true' : 'false' }}" data-target="#menu-students">
      <span class="flex items-center gap-2"><i class="fa-solid fa-users"></i><span>Students</span></span>
      <i class="fa-solid fa-chevron-down transition-transform duration-200 {{ $studentsOpen ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="menu-students" class="pl-3 mt-1 space-y-1 {{ $studentsOpen ? '' : 'hidden' }}">
      <a href="{{ route('headmaster.students.register') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.students.register') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-user-plus"></i><span>Registration</span></a>
      <a href="{{ route('headmaster.students.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.students.index') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-user-pen"></i><span>Manage</span></a>
      <a href="{{ route('headmaster.students.assign') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.students.assign') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-table-cells"></i><span>Assign Subjects</span></a>
    </div>
  </div>

  {{-- Teachers (dropdown) --}}
  @php $teachersOpen = request()->routeIs('headmaster.teachers.*'); @endphp
  <div>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-white menu-toggle" aria-expanded="{{ $teachersOpen ? 'true' : 'false' }}" data-target="#menu-teachers">
      <span class="flex items-center gap-2"><i class="fa-solid fa-chalkboard-user"></i><span>Teachers</span></span>
      <i class="fa-solid fa-chevron-down transition-transform duration-200 {{ $teachersOpen ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="menu-teachers" class="pl-3 mt-1 space-y-1 {{ $teachersOpen ? '' : 'hidden' }}">
      <a href="{{ route('headmaster.teachers.proposals') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.teachers.proposals') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-lightbulb"></i><span>Proposals</span></a>
      <a href="{{ route('headmaster.teachers.selected') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.teachers.selected') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-check-double"></i><span>Selected for Marking</span></a>
    </div>
  </div>

  {{-- Reports (dropdown) --}}
  @php $reportsOpen = request()->routeIs('headmaster.reports.*'); @endphp
  <div>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-white menu-toggle" aria-expanded="{{ $reportsOpen ? 'true' : 'false' }}" data-target="#menu-reports">
      <span class="flex items-center gap-2"><i class="fa-solid fa-file-lines"></i><span>Reports</span></span>
      <i class="fa-solid fa-chevron-down transition-transform duration-200 {{ $reportsOpen ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="menu-reports" class="pl-3 mt-1 space-y-1 {{ $reportsOpen ? '' : 'hidden' }}">
      <a href="{{ route('headmaster.reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.reports.index') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-file-lines"></i><span>All Reports</span></a>
      <a href="{{ route('headmaster.reports.results') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.reports.results') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-square-poll-vertical"></i><span>Results Reports</span></a>
    </div>
  </div>

  {{-- Institution (dropdown) --}}
  @php $instOpen = request()->routeIs('headmaster.institution.*'); @endphp
  <div>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-white menu-toggle" aria-expanded="{{ $instOpen ? 'true' : 'false' }}" data-target="#menu-institution">
      <span class="flex items-center gap-2"><i class="fa-regular fa-id-card"></i><span>Institution</span></span>
      <i class="fa-solid fa-chevron-down transition-transform duration-200 {{ $instOpen ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="menu-institution" class="pl-3 mt-1 space-y-1 {{ $instOpen ? '' : 'hidden' }}">
      <a href="{{ route('headmaster.institution.profile') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.institution.profile') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-regular fa-id-card"></i><span>Profile</span></a>
      <a href="{{ route('headmaster.institution.manage') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.institution.manage') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-gear"></i><span>Manage</span></a>
      <a href="{{ route('headmaster.institution.performance') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.institution.performance') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-chart-line"></i><span>Overall Performance</span></a>
    </div>
  </div>

  {{-- Support (single) --}}
  <div>
    <a href="{{ route('support.index') }}" class="mt-1 flex items-center justify-between px-3 py-2 rounded hover:bg-white">
      <span class="flex items-center gap-2"><i class="fa-solid fa-headset"></i><span>Contact NECTA/Technical Support</span></span>
    </a>
  </div>

  {{-- My Requests (dropdown) --}}
  @php $reqOpen = request()->routeIs('headmaster.requests.*'); @endphp
  <div>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-white menu-toggle" aria-expanded="{{ $reqOpen ? 'true' : 'false' }}" data-target="#menu-requests">
      <span class="flex items-center gap-2"><i class="fa-regular fa-envelope"></i><span>My Requests</span></span>
      <i class="fa-solid fa-chevron-down transition-transform duration-200 {{ $reqOpen ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="menu-requests" class="pl-3 mt-1 space-y-1 {{ $reqOpen ? '' : 'hidden' }}">
      <a href="{{ route('headmaster.requests.pending') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.requests.pending') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-regular fa-clock"></i><span>Pending Requests</span></a>
      <a href="{{ route('headmaster.requests.approved') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.requests.approved') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-circle-check"></i><span>Approved Requests</span></a>
      <a href="{{ route('headmaster.requests.need_approval') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.requests.need_approval') ? 'bg-white font-semibold text-emas-green' : '' }}"><i class="fa-solid fa-person-circle-question"></i><span>Need for Approval</span></a>
    </div>
  </div>

  {{-- Settings (single) --}}
  <div>
    <a href="{{ route('headmaster.settings.index') }}" class="mt-1 flex items-center justify-between px-3 py-2 rounded hover:bg-white {{ request()->routeIs('headmaster.settings.index') ? 'bg-white font-semibold text-emas-green' : '' }}">
      <span class="flex items-center gap-2"><i class="fa-solid fa-sliders"></i><span>System Preferences</span></span>
    </a>
  </div>

  <script>
    // Sidebar dropdown toggles (desktop + mobile): scope to this nav only
    (function(){
      const root = document.currentScript ? document.currentScript.closest('nav') : null;
      if (!root) return;
      root.querySelectorAll('.menu-toggle').forEach(btn => {
        const targetSel = btn.getAttribute('data-target');
        const target = root.querySelector(targetSel);
        btn.addEventListener('click', () => {
          const expanded = btn.getAttribute('aria-expanded') === 'true';
          btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
          const icon = btn.querySelector('.fa-chevron-down');
          if (icon) icon.classList.toggle('rotate-180');
          if (target) target.classList.toggle('hidden');
        });
      });
    })();
  </script>
</nav>
