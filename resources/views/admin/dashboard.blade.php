s
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        /* Custom sidebar transition */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Mobile Header -->
    <header class="bg-white shadow-md p-4 sm:hidden flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">Dashboard</h1>
        <button id="mobile-menu-button" class="text-blue-600 focus:outline-none">
            <i class="fa-solid fa-bars fa-2x"></i>
        </button>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gradient-to-b from-blue-600 to-blue-800 text-white w-64 space-y-6 p-6 fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 z-50">
            @php
                // Ambil data setting dari database sebagai array key-value
                $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
            @endphp

            <!-- Fancy Logo & Nama Aplikasi (Ukuran Lebih Kecil) -->
            <div class="flex flex-col items-center border-b border-blue-400 pb-4">
                <div class="relative flex flex-col items-center justify-center">
                    @if (isset($appSettings['logo']))
                        <div class="w-8 h-8 mb-1 relative">
                            <!-- Logo dengan efek hover zoom dan bayangan -->
                            <img src="{{ asset($appSettings['logo']) }}" alt="{{ $appSettings['name'] }}"
                                class="w-full h-full object-cover rounded-full border border-white shadow-md transform hover:scale-110 transition duration-300 ease-in-out">
                            <!-- Overlay gradient berdenyut -->
                            <div
                                class="absolute inset-0 rounded-full border border-transparent bg-gradient-to-r from-blue-400 to-green-400 opacity-50 animate-pulse">
                            </div>
                        </div>
                    @endif
                    @if (isset($appSettings['name']))
                        <h2 class="text-base font-semibold text-white tracking-wide drop-shadow">
                            {{ $appSettings['name'] }}
                        </h2>
                    @endif
                </div>
            </div>
            <!-- Logo/Title -->
            <div class="text-center border-b border-blue-400 pb-4">
                <h2 class="text-2xl font-bold">Admin Dashboard</h2>
            </div>
            <nav class="space-y-4">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-house w-5 mr-3"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>
                <a href="{{ route('status') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('status') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-file-alt w-5 mr-3"></i>
                    <span class="font-semibold">Lihat Laporan</span>
                </a>
                <a href="{{ route('complaint') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('complaint') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-comments w-5 mr-3"></i>
                    <span class="font-semibold">Lihat Pengaduan</span>
                </a>
                <a href="{{ route('user') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('user') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-users w-5 mr-3"></i>
                    <span class="font-semibold">Kelola Pengguna</span>
                </a>
                <a href="{{ route('location') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('location') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-map-marker-alt w-5 mr-3"></i>
                    <span class="font-semibold">Kelola Lokasi</span>
                </a>
                <a href="{{ route('setting') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('setting') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-cog w-5 mr-3"></i>
                    <span class="font-semibold">Kelola Pengaturan</span>
                </a>
            </nav>
            <!-- Sidebar Footer (Logout) -->
            <div class="border-t border-blue-400 pt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center p-2 rounded hover:bg-blue-700 transition-colors text-red-200">
                        <i class="fa-solid fa-right-from-bracket w-5 mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40 sm:hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 ml-0 sm:ml-64 p-6">
            <!-- Main Content Header -->
            <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-blue-600">Data Analisis Dashboard</h1>
                <span id="realTimeClock" class="text-gray-600">Memuat Waktu...</span>
            </div>

            <!-- Container Utama untuk Analitik -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Analytics Cards Grid: 2 kolom di mobile, 3 di tablet, 4 di desktop -->
                <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Card: Juru Bengkel -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-2">
                                <i class="fas fa-tools text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Juru Bengkel</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $juruBengkelCount }}</p>
                    </div>

                    <!-- Card: Petugas Kebersihan -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-2">
                                <i class="fas fa-broom text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Petugas Kebersihan</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-green-600">{{ $petugasKebersihanCount }}</p>
                    </div>

                    <!-- Card: Jumlah Laporan -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-2">
                                <i class="fas fa-file-alt text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Jumlah Laporan</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-purple-600">{{ $totalReports }}</p>
                    </div>

                    <!-- Card: Laporan Hari Ini -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-2">
                                <i class="fas fa-calendar-day text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Laporan Hari Ini</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-yellow-600">{{ $amountReportToday }}</p>
                    </div>

                    <!-- Card: Jumlah Komplain -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-red-100 text-red-600 mr-2">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Jumlah Komplain</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-red-600">{{ $totalComplaints }}</p>
                    </div>

                    <!-- Card: Petugas Lapor Hari Ini -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-2">
                                <i class="fas fa-user-check text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Petugas Lapor Hari Ini</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-indigo-600">{{ $countUsersWithReportToday }}</p>
                    </div>

                    <!-- Card: Petugas Belum Lapor Hari Ini -->
                    <div
                        class="bg-white shadow-md rounded-lg p-4 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-gray-100 text-gray-600 mr-2">
                                <i class="fas fa-user-times text-lg"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Belum Lapor Hari Ini</h2>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-gray-600">{{ $countUsersWithoutReportToday }}</p>
                    </div>
                </section>
            </div>

            <!-- Detail Table: User yang Sudah Membuat Laporan Hari Ini -->
            <section class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">User yang Sudah Membuat Laporan Hari Ini</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">No</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $no = 1;
                            @endphp
                            @foreach (App\Models\User::whereIn('role', ['juru-bengkel', 'petugas-kebersihan'])->get() as $userItem)
                                @php
                                    $hasReport = App\Models\Report::where('user_id', $userItem->id)
                                        ->whereDate('created_at', \Carbon\Carbon::today())
                                        ->exists();
                                @endphp
                                @if ($hasReport)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $no++ }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $userItem->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $userItem->email }}</td>
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </section>


            <!-- Detail Table: User yang Belum Lapor Hari Ini -->
            <section class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">User yang Belum Membuat Laporan Hari Ini</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">No</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $no = 1;
                            @endphp
                            @foreach (App\Models\User::whereIn('role', ['juru-bengkel', 'petugas-kebersihan'])->get() as $userItem)
                                @php
                                    $hasReport = App\Models\Report::where('user_id', $userItem->id)
                                        ->whereDate('created_at', \Carbon\Carbon::today())
                                        ->exists();
                                @endphp
                                @if (!$hasReport)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $no++ }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $userItem->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $userItem->email }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Scripts: Toggle Sidebar Mobile & Real-Time Clock -->
    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        mobileMenuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        function updateClock() {
            fetch('/server-time')
                .then(response => response.json())
                .then(data => {
                    const serverTime = new Date(data.time);
                    const hours = serverTime.getHours();
                    const minutes = serverTime.getMinutes();
                    const formattedMinutes = minutes.toString().padStart(2, '0');
                    const totalMinutes = hours * 60 + minutes;
                    let session = "Invalid";
                    if (totalMinutes >= 360 && totalMinutes < 720) {
                        session = "Pagi";
                    } else if (totalMinutes >= 720 && totalMinutes < 900) {
                        session = "Siang";
                    } else if (totalMinutes >= 900 && totalMinutes < 1020) {
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
        updateClock();
        setInterval(updateClock, 30000);
    </script>
</body>

</html>
