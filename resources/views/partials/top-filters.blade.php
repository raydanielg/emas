<div id="topFilters" class="flex items-center gap-2 w-full">
  <select id="filterRegion" class="h-9 rounded-md bg-white/95 text-slate-900 border border-emerald-700/40 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 min-w-[160px]"></select>
  <select id="filterDistrict" class="h-9 rounded-md bg-white/95 text-slate-900 border border-emerald-700/40 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 min-w-[160px]"></select>
  <select id="filterSchool" class="h-9 rounded-md bg-white/95 text-slate-900 border border-emerald-700/40 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 min-w-[280px]"></select>
  <select id="filterForm" class="h-9 rounded-md bg-white/95 text-slate-900 border border-emerald-700/40 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 min-w-[130px]"></select>
  <button id="filtersRefresh" class="h-9 w-9 flex items-center justify-center rounded-md bg-white/10 hover:bg-white/20 ring-1 ring-white/20" title="Refresh">
    <i class="fa-solid fa-rotate-right"></i>
  </button>
</div>

@push('scripts')
<script>
(function(){
  const regionSel = document.getElementById('filterRegion');
  const districtSel = document.getElementById('filterDistrict');
  const schoolSel = document.getElementById('filterSchool');
  const formSel = document.getElementById('filterForm');
  const btnRefresh = document.getElementById('filtersRefresh');

  @php($filters = session('filters') ?? ['region_id'=>null,'district_id'=>null,'school_id'=>null,'form'=>'all'])
  const current = @json($filters);

  function opt(value, label, selected){
    const o = document.createElement('option');
    o.value = value ?? '';
    o.textContent = label;
    if(selected) o.selected = true;
    return o;
  }

  async function fetchJson(url){
    const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    return await res.json();
  }

  async function loadRegions(){
    regionSel.innerHTML = '';
    regionSel.appendChild(opt('', 'Region', !current.region_id));
    const list = await fetchJson('{{ route('api.filters.regions') }}');
    list.forEach(r=> regionSel.appendChild(opt(r.id, r.name, String(r.id)===String(current.region_id))));
  }

  async function loadDistricts(){
    districtSel.innerHTML = '';
    districtSel.appendChild(opt('', 'District', !current.district_id));
    const params = current.region_id ? ('?region_id='+current.region_id) : '';
    const list = await fetchJson('{{ route('api.filters.districts') }}'+params);
    list.forEach(d=> districtSel.appendChild(opt(d.id, d.name, String(d.id)===String(current.district_id))));
  }

  async function loadSchools(){
    schoolSel.innerHTML = '';
    schoolSel.appendChild(opt('', 'School', !current.school_id));
    const params = current.district_id ? ('?district_id='+current.district_id) : '';
    const list = await fetchJson('{{ route('api.filters.schools') }}'+params);
    list.forEach(s=> schoolSel.appendChild(opt(s.id, s.name, String(s.id)===String(current.school_id))));
  }

  async function loadForms(){
    formSel.innerHTML = '';
    const list = await fetchJson('{{ route('api.filters.forms') }}');
    list.forEach(f=> formSel.appendChild(opt(f.id, f.name, String(f.id)===String(current.form))));
  }

  async function save(){
    const payload = {
      region_id: regionSel.value || null,
      district_id: districtSel.value || null,
      school_id: schoolSel.value || null,
      form: formSel.value || 'all',
      _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    await fetch('{{ route('api.filters.save') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': payload._token,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    });
  }

  function reload(){
    window.location.reload();
  }

  // events
  regionSel && regionSel.addEventListener('change', async ()=>{
    current.region_id = regionSel.value || null;
    current.district_id = null; current.school_id = null;
    await save();
    await loadDistricts();
    await loadSchools();
  });
  districtSel && districtSel.addEventListener('change', async ()=>{
    current.district_id = districtSel.value || null;
    current.school_id = null;
    await save();
    await loadSchools();
  });
  schoolSel && schoolSel.addEventListener('change', async ()=>{
    current.school_id = schoolSel.value || null;
    await save();
  });
  formSel && formSel.addEventListener('change', async ()=>{
    current.form = formSel.value || 'all';
    await save();
  });
  btnRefresh && btnRefresh.addEventListener('click', reload);

  // init
  (async ()=>{
    await loadRegions();
    await loadDistricts();
    await loadSchools();
    await loadForms();
  })();
})();
</script>
@endpush
