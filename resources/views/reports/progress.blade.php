@extends('layouts.user')

@section('title','Progress Report | eMAS')

@section('content')
<div id="progressApp" class="space-y-4" v-cloak>
  <h1 class="text-xl font-semibold">Progress Report</h1>

  <!-- Overall per-subject header summary (Vue rendered) -->
  <div class="bg-white rounded-xl ring-1 ring-gray-200 overflow-hidden">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 p-4">
      <div class="rounded-lg border border-slate-300 p-3 bg-slate-50">
        <div class="text-xs text-slate-600">Overall (All subjects)</div>
        <div class="mt-1 flex items-end justify-between">
          <div class="num-loading" :data-loading="loading">
            <div class="spinner flex items-center justify-center"><div class="lds-ring lds-xs"><div></div><div></div><div></div><div></div></div></div>
            <div class="number">
              <div class="text-lg font-semibold">@{{ format(overall.entered) }}</div>
              <div class="text-xs text-slate-500">of @{{ format(overall.registered) }}</div>
            </div>
          </div>
          <div class="num-loading" :data-loading="loading">
            <div class="spinner flex items-center justify-center"><div class="lds-ring lds-xs"><div></div><div></div><div></div><div></div></div></div>
            <div class="number text-sm font-semibold" :class="overall.pct>=100 ? 'text-emerald-700' : 'text-amber-700'">@{{ overall.pct }}%</div>
          </div>
        </div>
        <div class="mt-2 h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full" :class="overall.pct>=100 ? 'bg-emerald-600' : 'bg-amber-500'" :style="{width: overall.pct + '%'}"></div>
        </div>
      </div>

      <template v-for="sub in subjects" :key="sub.id">
        <div class="rounded-lg border border-slate-200 p-3">
          <div class="text-xs text-slate-500">@{{ sub.name }}</div>
          <div class="mt-1 flex items-end justify-between">
            <div class="num-loading" :data-loading="loading">
              <div class="spinner flex items-center justify-center"><div class="lds-ring lds-xs"><div></div><div></div><div></div><div></div></div></div>
              <div class="number">
                <div class="text-lg font-semibold">@{{ format(subjectTotals[sub.id]?.entered || 0) }}</div>
                <div class="text-xs text-slate-500">of @{{ format(subjectTotals[sub.id]?.registered || 0) }}</div>
              </div>
            </div>
            <div class="num-loading" :data-loading="loading">
              <div class="spinner flex items-center justify-center"><div class="lds-ring lds-xs"><div></div><div></div><div></div><div></div></div></div>
              <div class="number text-sm font-semibold" :class="(subjectTotals[sub.id]?.pct||0)>=100 ? 'text-emerald-700' : 'text-amber-700'">@{{ subjectTotals[sub.id]?.pct || 0 }}%</div>
            </div>
          </div>
          <div class="mt-2 h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full" :class="(subjectTotals[sub.id]?.pct||0)>=100 ? 'bg-emerald-600' : 'bg-amber-500'" :style="{width: (subjectTotals[sub.id]?.pct||0) + '%'}"></div>
          </div>
        </div>
      </template>
    </div>
  </div>

  <!-- Detailed per-school table (Vue rendered) -->
  <div class="bg-white rounded-xl ring-1 ring-gray-200 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-4 py-2">School</th>
          <th class="text-left px-4 py-2">District</th>
          <th class="text-right px-4 py-2">Registered</th>
          <th class="text-right px-4 py-2" v-for="sub in subjects" :key="'h'+sub.id">@{{ sub.name }}</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-t" v-for="s in schools" :key="s.id">
          <td class="px-4 py-2">@{{ s.name }}</td>
          <td class="px-4 py-2">@{{ s.district }}</td>
          <td class="px-4 py-2 text-right">@{{ format(registered[s.id] || 0) }}</td>
          <td class="px-4 py-2 text-right font-medium" v-for="sub in subjects" :key="'c'+s.id+'-'+sub.id">
            <span class="num-loading" :data-loading="loading">
              <span class="spinner inline-flex items-center justify-center"><span class="lds-ring lds-xs"><div></div><div></div><div></div><div></div></span></span>
              <span class="number min-w-[72px] text-right inline-block">@{{ format((enteredMap[s.id] && enteredMap[s.id][sub.id]) ? enteredMap[s.id][sub.id] : 0) }} / @{{ format(registered[s.id] || 0) }}</span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('head')
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
@endpush

@push('scripts')
<script>
  const { createApp } = Vue;
  createApp({
    data(){
      return {
        loading: true,
        subjects: [],
        schools: [],
        registered: {},
        enteredMap: {},
        subjectTotals: {},
        overall: { entered: 0, registered: 0, pct: 0 }
      }
    },
    methods: {
      format(n){ try { return new Intl.NumberFormat().format(n||0); } catch(_) { return n; } }
    },
    async mounted(){
      try {
        const res = await fetch('{{ route('reports.api.progress') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        this.subjects = data.subjects || [];
        this.schools = data.schools || [];
        this.registered = data.registered || {};
        this.enteredMap = data.enteredMap || {};
        this.subjectTotals = data.subjectTotals || {};
        this.overall = data.overall || { entered: 0, registered: 0, pct: 0 };
      } catch(e) { /* noop */ }
      finally {
        this.loading = false;
      }
    }
  }).mount('#progressApp');
</script>
@endpush
