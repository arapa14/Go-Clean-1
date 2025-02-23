<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Analisis Dashboard</title>
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
        <h1 class="text-xl font-bold text-blue-600">Analisis</h1>
        <button id="mobile-menu-button" class="text-blue-600 focus:outline-none">
            <i class="fa-solid fa-bars fa-2x"></i>
        </button>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gradient-to-b from-blue-600 to-blue-800 text-white w-64 space-y-6 p-6 fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 z-50">
            <!-- Logo/Title -->
            <div class="text-center border-b border-blue-400 pb-4">
                <h2 class="text-2xl font-bold">Reviewer Dashboard</h2>
            </div>
            <nav class="space-y-4">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-chart-line w-5 mr-3"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>
                <a href="{{ route('status') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('status') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-clipboard-list w-5 mr-3"></i>
                    <span class="font-semibold">Lihat Laporan</span>
                </a>
                <a href="{{ route('complaint') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('getComplaint') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-comments w-5 mr-3"></i>
                    <span class="font-semibold">Lihat Pengaduan</span>
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

            <!-- Analitik Cards -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Card Template -->
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Juru Bengkel</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $juruBengkelCount }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Petugas Kebersihan</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $petugasKebersihanCount }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Jumlah Laporan</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $totalReports }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Laporan Hari Ini</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $amountReportToday }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Jumlah Komplain</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $totalComplaints }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Petugas Lapor Hari Ini</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $countUsersWithReportToday }}</p>
                </div>
                <div
                    class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 hover:shadow-2xl transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Petugas Belum Lapor Hari Ini</h2>
                    <p class="mt-4 text-4xl font-bold text-blue-600">{{ $countUsersWithoutReportToday }}</p>
                </div>
            </section>

            <!-- Detail Table: User yang Belum Lapor Hari Ini -->
            <section class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">User yang Belum Membuat Laporan Hari Ini</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (App\Models\User::whereIn('role', ['juru-bengkel', 'petugas-kebersihan'])->get() as $userItem)
                                @php
                                    $hasReport = App\Models\Report::where('user_id', $userItem->id)
                                        ->whereDate('created_at', \Carbon\Carbon::today())
                                        ->exists();
                                @endphp
                                @if (!$hasReport)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $userItem->id }}</td>
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
