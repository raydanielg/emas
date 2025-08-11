@extends('layouts.headmaster')

@section('title','Subjects | Headmaster')

@section('content')
<style>
.modal { position: fixed; inset: 0; background: rgba(0,0,0,.4); display:none; align-items:center; justify-content:center; }
.modal.show { display:flex; }
</style>
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-book-open"></i>
      </div>

  {{-- Delete Confirmation Modal --}}
  <div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDeleteModal()"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md">
      <div class="bg-white rounded-xl ring-1 ring-slate-200 p-5 shadow-lg">
        <div class="flex items-start gap-3">
          <div class="h-10 w-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="flex-1">
            <div class="text-lg font-semibold">Delete Subject</div>
            <div class="text-slate-600 text-sm mt-1">Are you sure you want to delete <span id="delete-name" class="font-medium"></span>? This action cannot be undone.</div>
          </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button type="button" class="px-3 py-2 border rounded hover:bg-slate-50" onclick="closeDeleteModal()">Cancel</button>
          <form id="delete-form" method="post" action="#">
            @csrf
            @method('DELETE')
            <button id="delete-confirm" class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
              <span class="delete-icon"><i class="fa-regular fa-trash-can"></i></span>
              <span class="delete-text">Delete</span>
              <span class="delete-loading hidden"><i class="fa-solid fa-spinner fa-spin"></i></span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const deleteModal = document.getElementById('delete-modal');
    const deleteForm = document.getElementById('delete-form');
    const deleteName = document.getElementById('delete-name');
    const deleteBtn = document.getElementById('delete-confirm');
    function openDeleteModal(actionUrl, name){
      deleteForm.setAttribute('action', actionUrl);
      deleteName.textContent = name || 'this subject';
      deleteModal.classList.remove('hidden');
    }
    function closeDeleteModal(){
      deleteModal.classList.add('hidden');
      setDeleteLoading(false);
    }
    function setDeleteLoading(state){
      const icon = deleteBtn.querySelector('.delete-icon');
      const txt = deleteBtn.querySelector('.delete-text');
      const load = deleteBtn.querySelector('.delete-loading');
      if(state){
        deleteBtn.setAttribute('disabled','disabled');
        icon.classList.add('hidden');
        txt.textContent = 'Deleting...';
        load.classList.remove('hidden');
      }else{
        deleteBtn.removeAttribute('disabled');
        icon.classList.remove('hidden');
        txt.textContent = 'Delete';
        load.classList.add('hidden');
      }
    }
    deleteForm && deleteForm.addEventListener('submit', function(){ setDeleteLoading(true); });
  </script>
      <div>
        <h1 class="text-2xl font-bold leading-6">Subjects</h1>
        <div class="text-slate-500 text-sm">Manage subject codes and assign teachers</div>
      </div>
    </div>
    <button class="inline-flex items-center gap-2 px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90" onclick="openModal()"><i class="fa-solid fa-plus"></i><span>Register Subject</span></button>
  </div>

  @if(session('success'))
    <div class="mb-3 px-3 py-2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
      <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 border border-red-200">
      <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
  @endif

  <div class="bg-white rounded-xl p-4 ring-1 ring-slate-200">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-600 border-b">
            <th class="py-3 pr-3 font-medium"><i class="fa-solid fa-tag mr-2 text-slate-400"></i>Name</th>
            <th class="py-3 pr-3 font-medium"><i class="fa-solid fa-barcode mr-2 text-slate-400"></i>Code</th>
            <th class="py-3 pr-3 font-medium"><i class="fa-solid fa-chalkboard-user mr-2 text-slate-400"></i>Teacher</th>
            <th class="py-3 pr-3 font-medium"><i class="fa-solid fa-ellipsis mr-2 text-slate-400"></i>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subjects as $sub)
            @php $name = $sub->name ?? ('Subject '.$sub->id); @endphp
            <tr class="border-b hover:bg-slate-50/60">
              <td class="py-3 pr-3 font-medium">{{ $name }}</td>
              <td class="py-3 pr-3">
                <span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">{{ $sub->code ?? '-' }}</span>
              </td>
              <td class="py-3 pr-3">
                <div class="flex items-center gap-2">
                  <select class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-emas-green/30" onchange="assignTeacher({{ $sub->id }}, this.value)">
                    <option value="">-- No teacher --</option>
                    @foreach($teachers as $t)
                      <option value="{{ $t->id }}" {{ (int)($sub->teacher_id ?? 0) === (int)$t->id ? 'selected' : '' }}>{{ $t->display_name }}</option>
                    @endforeach
                  </select>
                  @if(!empty($sub->teacher_id))
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Assigned</span>
                  @else
                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">Unassigned</span>
                  @endif
                </div>
              </td>
              <td class="py-3 pr-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('headmaster.subjects.show', $sub->id) }}" title="View" class="inline-flex items-center gap-1 px-2 py-1 text-xs border rounded hover:bg-slate-50"><i class="fa-regular fa-eye"></i><span>View</span></a>
                  <a href="{{ route('headmaster.subjects.edit', $sub->id) }}" title="Edit" class="inline-flex items-center gap-1 px-2 py-1 text-xs border rounded hover:bg-slate-50"><i class="fa-regular fa-pen-to-square"></i><span>Edit</span></a>
                  <button type="button" title="Delete" class="inline-flex items-center gap-1 px-2 py-1 text-xs border border-red-600 text-red-600 rounded hover:bg-red-50" onclick="openDeleteModal('{{ route('headmaster.subjects.destroy', $sub->id) }}', '{{ addslashes($sub->name ?? 'this subject') }}')"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button>
                  <span id="indicator-{{ $sub->id }}" class="text-xs text-slate-500"></span>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-12">
                <div class="flex flex-col items-center justify-center text-slate-500">
                  <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3"><i class="fa-solid fa-book text-slate-400"></i></div>
                  <div class="font-medium">No subjects yet</div>
                  <div class="text-sm">Click “Register Subject” to add your first subject.</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="modal" class="modal">
  <div class="bg-white rounded-lg p-5 w-full max-w-md">
    <div class="text-lg font-semibold mb-3">Register Subject</div>
    <form method="post" action="{{ route('headmaster.subjects.store') }}">
      @csrf
      <div class="mb-3">
        <label class="block text-sm mb-1">Name</label>
        <input type="text" name="name" class="border rounded px-3 py-2 w-full" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm mb-1">Code</label>
        <input type="text" name="code" class="border rounded px-3 py-2 w-full" required>
      </div>
      <div class="mb-4">
        <label class="block text-sm mb-1">Assign Teacher (optional)</label>
        <select name="teacher_id" class="border rounded px-3 py-2 w-full">
          <option value="">-- None --</option>
          @foreach($teachers as $t)
            <option value="{{ $t->id }}">{{ $t->display_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-center gap-2 justify-end">
        <button type="button" class="px-3 py-2 border rounded" onclick="closeModal()">Cancel</button>
        <button class="px-3 py-2 bg-emas-green text-white rounded hover:bg-emas-green/90">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
function openModal(){ document.getElementById('modal').classList.add('show'); }
function closeModal(){ document.getElementById('modal').classList.remove('show'); }
async function assignTeacher(id, teacherId){
  const el = document.getElementById('indicator-'+id);
  el.textContent = 'Saving...';
  try{
    const res = await fetch(`{{ url('/headmaster/subjects') }}/${id}/assign-teacher`, {
      method: 'PATCH',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
      body: JSON.stringify({ teacher_id: teacherId || null })
    });
    const j = await res.json().catch(()=>({}));
    el.textContent = (res.ok && j.ok) ? 'Saved' : 'Failed';
  }catch(e){ el.textContent = 'Failed'; }
  setTimeout(()=> el.textContent = '', 1200);
}
</script>
@endsection
