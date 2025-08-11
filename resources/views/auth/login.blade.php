@extends('layouts.auth')

@section('title', 'eMAS | Sign In')

@section('content')
<div class="w-full max-w-md">
  <!-- Logo ABOVE and OUTSIDE the form box -->
  <div class="flex flex-col items-center mb-6">
    <img src="/logo-emas.svg" alt="eMAS" class="h-14 w-auto" />
  </div>

  <!-- Vue app mounts here to render the form -->
  <div id="login-app"></div>
</div>
@endsection

@push('head')
  <!-- Vue 3 via CDN -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
@endpush

@push('scripts')
  <script>
    const { createApp, ref, computed } = Vue;

    createApp({
      setup() {
        const username = ref('');
        const password = ref('');
        const showPwd  = ref(false);
        const loading  = ref(false);

        const canSubmit = computed(() => username.value.trim().length > 0 && password.value.length > 0);

        const onSubmit = (e) => {
          e.preventDefault();
          if (!canSubmit.value) return;
          loading.value = true;
          // Simulate async login, integrate with backend later
          setTimeout(() => { loading.value = false; e.target.submit && e.target.submit(); }, 600);
        };

        return { username, password, showPwd, loading, canSubmit, onSubmit };
      },
      template: `
        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-gray-200/70 p-8 login-container select-none" @contextmenu.prevent>
          <form method="POST" action="{{ url('/login') }}" role="form" class="space-y-5" autocomplete="off" @submit="onSubmit" @copy.prevent @cut.prevent @paste.prevent>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div>
              <label for="username" class="block text-sm font-semibold text-gray-900">Username</label>
              <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                  <!-- User icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M10 2a4 4 0 100 8 4 4 0 000-8z"/><path fill-rule="evenodd" d="M.458 16.042A9.956 9.956 0 0110 12c3.042 0 5.78 1.353 7.542 3.542A1 1 0 0116.9 17.4 8.004 8.004 0 0010 14a8.004 8.004 0 00-6.9 3.4 1 1 0 01-1.642-1.358z" clip-rule="evenodd"/></svg>
                </span>
                <input v-model.trim="username" type="text" id="username" name="username" required placeholder="Enter your username" autocomplete="off"
                       class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green/60" />
              </div>
            </div>

            <div>
              <label for="password" class="block text-sm font-semibold text-gray-900">Password</label>
              <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                  <!-- Lock icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 2a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7a5 5 0 00-5-5zm3 8H9V7a3 3 0 116 0v3z"/></svg>
                </span>
                <input :type="showPwd ? 'text' : 'password'" id="password" name="password" v-model="password" required placeholder="Enter your password" autocomplete="new-password"
                       class="block w-full rounded-xl border border-gray-200 pl-10 pr-10 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green/60" />
                <button type="button" aria-label="Toggle password visibility" @click="showPwd = !showPwd"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                  <!-- Eye icon -->
                  <svg v-if="!showPwd" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M3 3l18 18-1.5 1.5L17.7 20.7C16.2 21.5 14.6 22 12 22 5 22 2 15 2 15s1-2.2 3-4.3L1.5 4.5 3 3z"/></svg>
                </button>
              </div>
            </div>

            <button type="submit" :disabled="!canSubmit || loading"
                    class="w-full h-11 rounded-xl bg-gradient-to-r from-emas-green to-emas-greenDark hover:from-emerald-500 hover:to-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold transition-all shadow-md hover:shadow-lg flex items-center justify-center">
              <template v-if="!loading">
                <span>Sign In</span>
              </template>
              <template v-else>
                <div class="lds-facebook" style="width:32px;height:32px;color:#fff;">
                  <div style="left:4px;width:8px"></div>
                  <div style="left:12px;width:8px"></div>
                  <div style="left:20px;width:8px"></div>
                </div>
              </template>
            </button>
          </form>

          <div class="mt-5 flex items-center justify-between text-sm">
            <a href="{{ url('/password/forgot') }}" class="font-semibold text-emas-green hover:text-emas-greenDark">Forgot password?</a>
            <span class="text-gray-400">•</span>
            <span class="text-gray-500">Need access? Contact Admin</span>
          </div>
        </div>
      `
    }).mount('#login-app');

    // Global UI hardening (client-side):
    // 1) Disable right-click everywhere
    document.addEventListener('contextmenu', (e) => e.preventDefault(), true);
    // 2) Block common shortcuts (Ctrl+U, Ctrl+C, Ctrl+Shift+I, F12)
    document.addEventListener('keydown', function(e){
      const k = e.key?.toLowerCase?.() || '';
      if ((e.ctrlKey && k === 'u') ||                      // View source
          (e.ctrlKey && k === 'c') ||                      // Copy
          (e.ctrlKey && e.shiftKey && k === 'i') ||        // DevTools
          (k === 'f12')) {                                 // DevTools
        e.preventDefault();
        e.stopPropagation();
      }
    }, true);
  </script>
@endpush
