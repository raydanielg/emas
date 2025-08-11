@extends('layouts.headmaster')

@section('title', ($subject->name ?? ('Subject '.$subject->id)).' | Headmaster')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-lg bg-emas-green/10 text-emas-green flex items-center justify-center">
        <i class="fa-solid fa-book-open"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold leading-6">Subject Details</h1>
        <div class="text-slate-500 text-sm">View subject info and actions</div>
      </div>
    </div>
    <a href="{{ route('headmaster.subjects.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
  </div>

  <div class="bg-white rounded-xl p-5 ring-1 ring-slate-200 space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <div class="text-slate-500 text-xs uppercase tracking-wide">Subject Name</div>
        <div class="mt-1 text-lg font-semibold">{{ $subject->name ?? ('Subject '.$subject->id) }}</div>
      </div>
      <div>
        <div class="text-slate-500 text-xs uppercase tracking-wide">Code</div>
        <div class="mt-1"><span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">{{ $subject->code ?? '-' }}</span></div>
      </div>
      <div>
        <div class="text-slate-500 text-xs uppercase tracking-wide">Assigned Teacher</div>
        <div class="mt-1 text-lg">{{ $teacher->display_name ?? '—' }}</div>
      </div>
    </div>

    <div class="pt-2 flex items-center gap-2">
      <button type="button" class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="openDeleteModal('{{ route('headmaster.subjects.destroy', $subject->id) }}', '{{ addslashes($subject->name ?? ('Subject '.$subject->id)) }}')"><i class="fa-regular fa-trash-can"></i><span>Delete Subject</span></button>
    </div>
  </div>
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
</div>
@endsection
