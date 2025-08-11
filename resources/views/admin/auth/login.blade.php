<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white p-6 rounded-lg shadow ring-1 ring-slate-200">
    <h1 class="text-xl font-semibold mb-4">Admin Login</h1>
    @if($errors->any())
      <div class="mb-3 p-3 rounded bg-rose-50 text-rose-800 ring-1 ring-rose-200">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border-slate-300 focus:border-emas-green focus:ring-emas-green" required />
      </div>
      <div>
        <label class="block text-sm text-slate-700">Password</label>
        <input type="password" name="password" class="mt-1 w-full rounded border-slate-300 focus:border-emas-green focus:ring-emas-green" required />
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" id="remember" name="remember" value="1" class="rounded border-slate-300" />
        <label for="remember" class="text-sm text-slate-600">Remember me</label>
      </div>
      <button class="w-full py-2 rounded bg-emas-green text-white hover:bg-emerald-600">Login</button>
    </form>
  </div>
</body>
</html>
