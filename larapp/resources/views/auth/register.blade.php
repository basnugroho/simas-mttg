<x-auth.layout :title="'Daftar'">
  <h2 class="text-3xl font-extrabold">Buat Akun</h2>
  <p class="text-white/60 mt-2 mb-8">Daftarkan diri anda untuk mengakses aplikasi.</p>

  <form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <div>
      <x-auth.label for="name" value="Nama Lengkap" />
      <x-auth.input id="name" name="name" placeholder="nama anda" value="{{ old('name') }}" required />
      <x-auth.error :messages="$errors->get('name')" />
    </div>

    <div>
      <x-auth.label for="username" value="Username" />
      <x-auth.input id="username" name="username" placeholder="username unik" value="{{ old('username') }}" required />
      <x-auth.error :messages="$errors->get('username')" />
    </div>

    <div>
      <x-auth.label for="email" value="Email (opsional)" />
      <x-auth.input id="email" name="email" type="email" placeholder="email@contoh.com" value="{{ old('email') }}" />
      <x-auth.error :messages="$errors->get('email')" />
    </div>

    <div>
      <x-auth.label for="password" value="Password" />
      <x-auth.input id="password" name="password" type="password" placeholder="min. 8 karakter" required />
      <x-auth.error :messages="$errors->get('password')" />
    </div>

    <div>
      <x-auth.label for="password_confirmation" value="Konfirmasi Password" />
      <x-auth.input id="password_confirmation" name="password_confirmation" type="password" required />
    </div>

    <button type="submit" class="btn-red w-full text-white font-semibold py-3 rounded-lg shadow-md">
      Daftar
    </button>

    <p class="text-center text-white/70 text-sm">
      Sudah punya akun?
      <a href="{{ route('login') }}" class="text-white font-semibold underline">Masuk</a>
    </p>
  </form>
</x-auth.layout>
