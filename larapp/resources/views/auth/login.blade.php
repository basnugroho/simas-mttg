<x-auth.layout :title="'Masuk'">
  <h2 class="text-3xl font-extrabold">Log In Sekarang</h2>
  <p class="text-white/60 mt-2 mb-8">Silahkan masuk menggunakan username dan password anda</p>

  <form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
      <x-auth.label for="username" value="Username" />
      <x-auth.input id="username" name="username" placeholder="masukkan username" value="{{ old('username') }}" required autofocus />
      <x-auth.error :messages="$errors->get('username')" />
    </div>

    <div>
      <x-auth.label for="password" value="Password" />
      <div class="relative">
        <x-auth.input id="password" name="password" type="password" placeholder="masukkan password" required />
        <button type="button" data-toggle-pass
          class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80">
          👁
        </button>
      </div>
      <x-auth.error :messages="$errors->get('password')" />
    </div>

    <div class="flex items-center justify-between text-sm">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="remember" class="rounded border-gray-600 bg-gray-800">
        <span class="text-white/80">Ingat saya</span>
      </label>
      <a href="{{ route('password.request') }}" class="text-white/70 hover:text-white">Lupa password?</a>
    </div>

    <button type="submit" class="btn-red w-full text-white font-semibold py-3 rounded-lg shadow-md">
      Login
    </button>

    <p class="text-center text-white/70 text-sm">
      Belum punya akun?
      <a href="{{ route('register') }}" class="text-white font-semibold underline">Daftar</a>
    </p>
  </form>
</x-auth.layout>
