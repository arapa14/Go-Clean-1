<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        /* Tema kebiruan dengan gradient */
        body {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        /* Animasi fade-in untuk konten */
        .fade-in {
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Animasi pulse untuk ikon */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center">
    <div
        class="bg-white bg-opacity-90 backdrop-filter backdrop-blur-lg rounded-xl p-10 shadow-2xl text-center max-w-lg mx-4 fade-in">
        <div class="mb-6">
            <!-- Ikon forbidden dengan animasi pulse -->
            <i class="fa-solid fa-ban text-blue-500 text-7xl pulse"></i>
        </div>
        <h1 class="text-6xl font-bold text-blue-600">403</h1>
        <p class="mt-4 text-xl text-blue-500">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <p class="mt-2 text-md text-blue-400">Akses ke halaman yang Anda minta dibatasi atau akun Anda tidak memiliki
            hak yang diperlukan.</p>
        <a href="{{ url('/') }}"
            class="mt-8 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded transition duration-300">
            Kembali ke Beranda
        </a>
    </div>
    <footer class="mt-10 text-center">
        <p class="text-white text-sm">&copy; {{ date('Y') }} SMK Negeri 1 Jakarta. All rights reserved.</p>
    </footer>
</body>

</html>
