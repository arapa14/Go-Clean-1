<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lupa Password</title>
  <link rel="icon" type="image/png" href="{{ asset($logo) }}" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center p-6">
  <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md relative">
    <!-- Tombol kembali ke login (posisi pojok kiri atas) -->
    <div class="absolute top-4 left-4">
      <a href="{{ route('auth.index') }}" class="text-purple-600 hover:text-purple-800">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
    </div>
    <!-- Logo Aplikasi -->
    <div class="flex justify-center mb-6">
      <img src="{{ asset($logo) }}" alt="{{ $name }}" class="w-20 h-20 object-cover rounded-full">
    </div>
    <!-- Nama Aplikasi dan Judul Halaman -->
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">{{ $name }}</h1>
    <h2 class="text-xl text-center text-gray-600 mb-6">Lupa Password</h2>

    <!-- Pesan Sukses/Error -->
    @if (session('success'))
      <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 border border-green-200 rounded">
        {{ session('success') }}
      </div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 border border-red-200 rounded">
        {{ session('error') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 border border-red-200 rounded">
        {{ $errors->first() }}
      </div>
    @endif

    <!-- Form Lupa Password -->
    <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
      @csrf
      <div>
        <label for="email" class="block text-gray-700 font-medium mb-2">Email Anda</label>
        <input type="email" name="email" id="email" required placeholder="Masukkan Email Anda" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
        @error('email')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
      <button type="submit" class="w-full py-3 text-lg font-bold text-white bg-purple-600 rounded hover:bg-purple-700 transition">
        Kirim Link Reset Password
      </button>
    </form>
  </div>
</body>
</html>
