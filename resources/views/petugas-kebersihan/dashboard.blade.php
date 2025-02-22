<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- header --}}
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
        <!-- Nama Pengguna -->
        <h1 class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 sm:text-lg">Welcome {{ $user->name }}</h1>

        <!-- Jam (Real-time) -->
        <span id="realTimeClock" class="text-gray-600 mb-2 sm:mb-0 sm:text-md">Memuat Waktu...</span>

        <!-- Tombol Logout dengan Ikon -->
        <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="flex items-center text-red-600 hover:text-red-800 text-lg w-full sm:w-auto">
                <!-- Ikon Logout -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>

    {{-- button --}}
    <div class="text-center mt-6">
        <a href='{{ route('riwayat') }}'
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full sm:w-auto cursor-pointer">
            Lihat Riwayat
        </a>
    </div>

    <div class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold">Submit Laporan</h2>
        @if ($errors->any())
            <div id="error-message" class="p-3 mt-3 text-sm text-red-600 bg-red-100 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div id="success-message" class="p-3 mt-3 text-sm text-green-600 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Input Upload Gambar -->
            <div>
                <input type="file" name="images" id="images" capture="environment" required
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            {{-- location input --}}
            <div class="mt-4">
                <select name="location" class="w-full p-2 border rounded-md">
                    <option>Pilih lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->location }}">{{ $location->location }}</option>
                    @endforeach
                </select>
            </div>

            {{-- description input --}}
            <div class="mt-4">
                <input type="text" name="description" placeholder="Deskripsi" class="w-full p-2 border rounded-md">
            </div>
            <div class="mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full">Submit</button>
            </div>
        </form>
    </div>

    <div class="mt-6">
        @if ($reportToday->isEmpty())
            <div class="flex flex-col items-center justify-center p-6 bg-white shadow-md rounded-lg">
                <!-- Ilustrasi Ikon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-blue-400" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>

                <h3 class="mt-4 text-xl font-semibold text-gray-700">Belum Ada Laporan Hari Ini</h3>
                <p class="text-gray-500 text-sm text-center max-w-sm mt-1">Saat ini belum ada laporan yang masuk.
                    Silakan submit laporan pertama Anda untuk hari ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($reportToday as $report)
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border p-4">
                        <div
                            class="w-full max-h-60 flex items-center justify-center bg-gray-200 rounded-md overflow-hidden">
                            <img src="{{ asset('storage/' . $report->image) }}" alt="report Image"
                                class="w-full h-auto object-contain aspect-[4/3]">
                        </div>

                        <div class="p-4">
                            <h3 class="font-semibold text-blue-600 text-lg mb-1">{{ ucfirst($report->name) }}</h3>
                            <p class="text-gray-500 text-sm">
                                <span class="text-blue-500 font-medium">Laporan : </span>
                                <span
                                    class="text-blue-500 font-medium bg-blue-100 px-2 py-1 rounded-md">{{ $report->session }}</span>
                            </p>
                            <p class="text-gray-700 mt-2">{{ $report->description }}</p>
                        </div>

                        <div class="flex justify-between items-center px-4 py-3 text-sm">
                            <span
                                class="text-gray-400 font-medium">{{ $report->location ?? 'Tidak Diketahui' }}</span>
                            <div class="flex space-x-2">
                                <span
                                    class="px-3 py-1 font-semibold rounded-lg
                        @if ($report->status == 'approved') bg-green-100 text-green-600 
                        @elseif($report->status == 'rejected') bg-red-100 text-red-600
                        @else bg-yellow-100 text-yellow-600 @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function updateClock() {
            fetch('/server-time')
                .then(response => response.json())
                .then(data => {
                    const serverTime = new Date(data.time);
                    const hours = serverTime.getHours();
                    const minutes = serverTime.getMinutes();
                    const formattedMinutes = minutes.toString().padStart(2, '0');

                    // Konversi jam ke menit total untuk perbandingan waktu yang akurat
                    const totalMinutes = hours * 60 + minutes;

                    let session = "Invalid"; // Default
                    if (totalMinutes >= 360 && totalMinutes < 720) { // 6:00 - 12:00
                        session = "Pagi";
                    } else if (totalMinutes >= 720 && totalMinutes < 900) { // 12:00 - 15:00
                        session = "Siang";
                    } else if (totalMinutes >= 900 && totalMinutes < 1020) { // 15:00 - 17:00
                        session = "Sore";
                    }

                    const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                        "September", "Oktober", "November", "Desember"
                    ];

                    const dayName = days[serverTime.getDay()];
                    const day = serverTime.getDate();
                    const month = months[serverTime.getMonth()];
                    const year = serverTime.getFullYear();

                    document.getElementById('realTimeClock').innerText =
                        `${session}, ${dayName} ${day} ${month} ${year} - ${hours.toString().padStart(2, '0')}:${formattedMinutes}`;
                })
                .catch(error => console.error("Gagal mengambil waktu server:", error));
        }

        // Panggil saat halaman dimuat
        updateClock();

        // Update setiap 30 detik
        setInterval(updateClock, 30000);

        // Menghilangkan pesan setelah 5 detik
        setTimeout(function() {
            let errorMessage = document.getElementById('error-message');
            let successMessage = document.getElementById('success-message');

            if (errorMessage) {
                errorMessage.style.transition = "opacity 0.5s";
                errorMessage.style.opacity = "0";
                setTimeout(() => errorMessage.remove(), 500);
            }

            if (successMessage) {
                successMessage.style.transition = "opacity 0.5s";
                successMessage.style.opacity = "0";
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>