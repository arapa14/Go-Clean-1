<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $name }}</title>
    <link rel="icon" type="image/png" href="{{ asset($logo)}}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-200 to-blue-600 flex items-center justify-center p-6">
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden w-full max-w-4xl">
        <div class="md:flex">
            <!-- Bagian Gambar (hanya tampil di desktop) -->
            <div class="hidden md:block md:w-1/2">
                <img src="{{ asset($logo) }}" alt="{{ $name }}" class="object-cover h-full w-full">
            </div>
            <!-- Bagian Form Login -->
            <div class="w-full md:w-1/2 p-8">
                <h1 class="text-4xl font-extrabold text-center text-blue-700 mb-4">{{ $name }}</h1>

                <!-- Pesan Success -->
                @if (session('success'))
                    <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Pesan Error -->
                @if (session('error'))
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Pesan error validasi (jika ada) -->
                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-gray-800 font-semibold mb-2">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email Anda"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-gray-800 font-semibold mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required placeholder="Masukkan password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <!-- Tombol toggle password dengan ikon FontAwesome -->
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-3 flex items-center">
                                <i id="toggleIcon" class="fa-regular fa-eye text-gray-600 text-lg"></i>
                            </button>
                        </div>
                    </div>
                    

                    <div>
                        <button type="submit"
                            class="w-full py-3 text-lg font-bold text-white bg-blue-700 rounded-md hover:bg-blue-800 transition duration-300">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script untuk toggle password -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Ganti ikon menjadi eye-slash
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.remove('fa-regular');
                toggleIcon.classList.add('fa-solid', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                // Kembalikan ikon ke eye
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.remove('fa-solid');
                toggleIcon.classList.add('fa-regular', 'fa-eye');
            }
        }
    </script>
</body>

</html>
