<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

const identifier = ref('')

function submit(e) {
  e.preventDefault()
  // TODO: Wire to backend route
}
</script>

<template>
  <Head title="Forgot Password" />
  <div class="min-h-screen w-full flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
      <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl border-t-4 border-emas-green p-8">
        <div class="flex flex-col items-center mb-6">
          <img src="/logo-emas.svg" alt="eMAS" class="h-12 w-auto mb-3" />
          <h2 class="text-2xl font-bold tracking-wide text-gray-900 text-center">
            <span class="text-emas-green lowercase">e</span><span>MAS</span>
            <span class="text-base font-medium text-gray-500 align-middle">— Forgot password</span>
          </h2>
        </div>
        <form @submit="submit" class="space-y-5">
          <div>
            <label for="identifier" class="block text-sm font-semibold text-gray-900">Username or Email</label>
            <div class="relative mt-1">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M10 2a4 4 0 100 8 4 4 0 000-8z"/><path fill-rule="evenodd" d="M.458 16.042A9.956 9.956 0 0110 12c3.042 0 5.78 1.353 7.542 3.542A1 1 0 0116.9 17.4 8.004 8.004 0 0010 14a8.004 8.004 0 00-6.9 3.4 1 1 0 01-1.642-1.358z" clip-rule="evenodd"/></svg>
              </span>
              <input v-model="identifier" id="identifier" type="text" required placeholder="Enter your username or email"
                     class="block w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emas-green focus:border-emas-green" />
            </div>
          </div>
          <button type="submit" class="w-full rounded-lg bg-emas-green hover:bg-emas-greenDark text-white font-semibold py-2.5 transition-colors shadow-sm">Send reset link</button>
        </form>
        <div class="mt-5 flex items-center justify-between text-sm">
          <a href="/login" class="font-semibold text-emas-green hover:text-emas-greenDark">Back to sign in</a>
          <span class="text-gray-500">Need help? Contact Admin</span>
        </div>
      </div>
    </div>
  </div>
</template>
