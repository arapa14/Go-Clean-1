<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Settings Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Global Layout */
        body {
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar & Main Content Transition */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* DataTables Customization */
        table.dataTable thead {
            background-color: #f3f4f6;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #e5e7eb;
            color: #374151 !important;
            margin: 0 0.125rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #d1d5db;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            outline: none;
        }

        table.dataTable thead th {
            color: #374151;
            font-weight: 600;
        }

        .action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 9999px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .action-icon:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }

        .btn-edit {
            background-color: #3b82f6;
            color: #ffffff;
        }

        .btn-delete {
            background-color: #f87171;
            color: #ffffff;
        }

        @media (min-width: 640px) {
            main {
                margin-left: 16rem;
            }
        }

        /* Custom SweetAlert2 Styles */
        .swal2-popup {
            border-radius: 1rem;
            font-size: 1rem;
            padding: 1.5rem;
        }

        .swal2-title {
            font-weight: 700;
            color: #111827;
        }

        .swal2-content {
            color: #374151;
        }

        .swal2-input,
        .swal2-select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.75rem;
        }

        .swal2-confirm,
        .swal2-cancel {
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .swal2-confirm {
            background-color: #3b82f6;
            color: #ffffff;
            margin: 0.5rem;
        }

        .swal2-cancel {
            background-color: #f87171;
            color: #ffffff;
            margin: 0.5rem;
        }

        @media (max-width: 640px) {
            .swal2-popup {
                width: 90% !important;
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <!-- Mobile Header -->
    <header class="bg-white shadow-md p-4 sm:hidden flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">Manage Settings</h1>
        <button id="mobile-menu-button" class="text-blue-600 focus:outline-none">
            <i class="fa-solid fa-bars fa-2x"></i>
        </button>
    </header>

    <div class="flex flex-1">
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40 sm:hidden"></div>
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gradient-to-b from-blue-600 to-blue-800 text-white w-64 space-y-6 p-6 fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 z-50">
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
                    <span class="font-semibold">Manage Users</span>
                </a>
                <a href="{{ route('location') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('location') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-map-marker-alt w-5 mr-3"></i>
                    <span class="font-semibold">Manage Locations</span>
                </a>
                <a href="{{ route('setting') }}"
                    class="flex items-center p-2 rounded transition-colors {{ request()->routeIs('setting') ? 'bg-blue-700 text-white' : 'hover:bg-blue-700' }}">
                    <i class="fa-solid fa-cog w-5 mr-3"></i>
                    <span class="font-semibold">Manage Settings</span>
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

        <!-- Main Content -->
        <main class="flex-1 min-w-0 ml-0 sm:ml-64 p-4 sm:p-6 relative z-106">
            <div class="mt-6 bg-white shadow-md rounded-lg">
                <!-- Header Halaman Settings -->
                <div
                    class="bg-blue-600 shadow-md rounded-t-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h1 class="text-2xl font-bold text-white">Settings Management</h1>
                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                        <span id="realTimeClock" class="bg-white/20 text-white px-3 py-1 rounded-md font-medium">
                            Memuat Waktu...
                        </span>
                    </div>
                </div>

                <!-- Konten Utama Settings -->
                <div class="p-6">
                    <!-- Form Settings -->
                    <form id="updateSettingForm" action="{{ route('setting.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Pengaturan Waktu -->
                        <div class="mb-8 p-4 border rounded-lg bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-clock"></i> Pengaturan Waktu
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <label for="enable_time_restriction"
                                        class="block text-gray-700 font-semibold sm:w-1/2">
                                        Aktifkan Pembatasan Waktu:
                                    </label>
                                    <select id="enable_time_restriction" name="enable_time_restriction"
                                        class="w-full sm:w-1/3 border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="1"
                                            {{ $settings['enable_time_restriction'] == '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0"
                                            {{ $settings['enable_time_restriction'] == '0' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <label for="enable_session_restriction"
                                        class="block text-gray-700 font-semibold sm:w-1/2">
                                        Aktifkan Pembatasan Sesi:
                                    </label>
                                    <select id="enable_session_restriction" name="enable_session_restriction"
                                        class="w-full sm:w-1/3 border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="1"
                                            {{ $settings['enable_session_restriction'] == '1' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="0"
                                            {{ $settings['enable_session_restriction'] == '0' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal Sesi -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-calendar-alt"></i> Jadwal Sesi
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Card Pagi -->
                                <div class="p-4 border rounded-lg bg-gray-50">
                                    <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Pagi</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label for="pagi_start"
                                                class="block text-gray-600 text-sm font-medium">Mulai (Jam):</label>
                                            <input type="number" id="pagi_start" name="pagi_start"
                                                value="{{ $settings['pagi_start'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                        <div>
                                            <label for="pagi_end"
                                                class="block text-gray-600 text-sm font-medium">Berakhir (Jam):</label>
                                            <input type="number" id="pagi_end" name="pagi_end"
                                                value="{{ $settings['pagi_end'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Siang -->
                                <div class="p-4 border rounded-lg bg-gray-50">
                                    <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Siang</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label for="siang_start"
                                                class="block text-gray-600 text-sm font-medium">Mulai (Jam):</label>
                                            <input type="number" id="siang_start" name="siang_start"
                                                value="{{ $settings['siang_start'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                        <div>
                                            <label for="siang_end"
                                                class="block text-gray-600 text-sm font-medium">Berakhir (Jam):</label>
                                            <input type="number" id="siang_end" name="siang_end"
                                                value="{{ $settings['siang_end'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Sore -->
                                <div class="p-4 border rounded-lg bg-gray-50">
                                    <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Sore</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label for="sore_start"
                                                class="block text-gray-600 text-sm font-medium">Mulai (Jam):</label>
                                            <input type="number" id="sore_start" name="sore_start"
                                                value="{{ $settings['sore_start'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                        <div>
                                            <label for="sore_end"
                                                class="block text-gray-600 text-sm font-medium">Berakhir (Jam):</label>
                                            <input type="number" id="sore_end" name="sore_end"
                                                value="{{ $settings['sore_end'] }}"
                                                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Sistem -->
                        <div class="mb-8 p-4 border rounded-lg bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-cog"></i> Pengaturan Sistem
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="nama_sistem" class="block text-gray-700 font-semibold">Nama
                                        Sistem:</label>
                                    <input type="text" id="nama_sistem" name="nama_sistem"
                                        value="{{ $settings['nama_sistem'] ?? '' }}"
                                        class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label for="logo_sistem" class="block text-gray-700 font-semibold">Logo
                                        Sistem:</label>
                                    <input type="file" id="logo_sistem" name="logo_sistem" accept="image/*"
                                        class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    @if (isset($settings['logo']) && $settings['logo'])
                                        <div class="mt-2">
                                            <img src="{{ asset($logo) }}" alt="Logo Sistem"
                                                class="h-20">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="text-center">
                            <button type="submit"
                                class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold px-8 py-3 rounded-md shadow-md transition duration-300">
                                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Notifikasi SweetAlert2 untuk flash messages -->
    @if (session('success'))
        <script>
            Swal.fire('Berhasil', '{{ session('success') }}', 'success');
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire('Gagal', '{{ session('error') }}', 'error');
        </script>
    @endif
    @if (session('info'))
        <script>
            Swal.fire('Info', '{{ session('info') }}', 'info');
        </script>
    @endif

    <script>
        // Mobile sidebar toggle
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
    </script>

    <script>
        // Update real time clock
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

    <script>
        // Konfirmasi dan update settings via AJAX
        document.getElementById('updateSettingForm').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Apakah Anda yakin ingin mengubah pengaturan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = event.target;
                    const formData = new FormData(form);
                    fetch("{{ route('setting.update') }}", {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(async response => {
                            if (!response.ok) {
                                const errorData = await response.json();
                                throw errorData;
                            }
                            return response.json();
                        })
                        .then(resultData => {
                            if (resultData.success) {
                                Swal.fire("Berhasil!", resultData.success, "success")
                                    .then(() => location.reload());
                            } else if (resultData.info) {
                                Swal.fire("Info", resultData.info, "info");
                            } else {
                                Swal.fire("Gagal!", resultData.error || 'Terjadi kesalahan', "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error update settings:', error);
                            let message = "Terjadi kesalahan, silahkan coba lagi";
                            if (error.errors) {
                                message = Object.values(error.errors).join('<br>');
                            } else if (error.message) {
                                message = error.message;
                            }
                            Swal.fire("Error", message, "error");
                        });
                }
            });
        });
    </script>
</body>

</html>
