<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Laporan</title>
    .@vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* (Opsional) Jika ingin mencegah scroll horizontal global,
       pastikan tidak mengganggu elemen yang sengaja overflow */
        html,
        body {
            /*overflow-x: hidden;*/
            /* Pertimbangkan untuk menghapus atau menonaktifkannya */
        }

        /* Custom transition untuk sidebar */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        table.dataTable thead {
            background-color: #f3f4f6;
        }

        /* Custom styling untuk DataTables agar selaras dengan Tailwind */
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

        /* Styling untuk ikon aksi */
        .action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 9999px;
            transition: background-color 0.3s;
        }

        .action-icon:hover {
            opacity: 0.85;
        }

        .btn-view {
            background-color: #3b82f6;
            color: #ffffff;
        }

        .btn-download {
            background-color: #10b981;
            color: #ffffff;
        }

        /* Pastikan main tidak memicu overflow di layar kecil */
        @media (min-width: 640px) {
            main {
                margin-left: 16rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Mobile Header -->
    <div
        class="bg-white shadow-md rounded-lg p-4 flex flex-wrap sm:flex-row justify-between items-center mb-6 sm:hidden">
        <h1 class="text-xl font-bold text-blue-600">Status Laporan</h1>
        <button id="mobile-menu-button" class="text-blue-600 focus:outline-none">
            <i class="fa-solid fa-bars fa-2x"></i>
        </button>
    </div>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gradient-to-b from-blue-600 to-blue-800 text-white w-64 space-y-6 p-6
             fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 z-50">
            <!-- Logo/Title -->
            <div class="text-center border-b border-blue-400 pb-4">
                <h2 class="text-2xl font-bold">
                    {{ Auth::check() ? (Auth::user()->role == 'reviewer' ? 'Reviewer Dashboard' : (Auth::user()->role == 'admin' ? 'Admin Dashboard' : 'Dashboard')) : 'Dashboard' }}
                </h2>
            </div>

            <!-- Navigation -->
            <nav class="space-y-4">
                @if (Auth::check())
                    @if (Auth::user()->role == 'reviewer')
                        <!-- Menu untuk Reviewer: hanya Dashboard, Lihat Laporan, dan Lihat Pengaduan -->
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
                    @elseif(Auth::user()->role == 'admin')
                        <!-- Menu untuk Admin: semua menu -->
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
                    @endif
                @endif
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

        <!-- Overlay untuk mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40 sm:hidden"></div>

        <!-- Main Content -->
        <!-- Tambahkan min-w-0 agar elemen bisa mengecil sesuai ruang dalam flex container -->
        <main class="flex-1 min-w-0 ml-0 sm:ml-64 p-4 sm:p-6 relative z-10">
            <!-- Header Main Content -->
            <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600">Status Laporan</h1>
                <span id="realTimeClock" class="text-gray-600 mt-2 sm:mt-0">Memuat Waktu...</span>
            </div>

            <!-- DataTable Container -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6">
                <div class="mb-4">
                    <button id="approve-all-btn"
                        class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        Approve Semua
                    </button>
                </div>
                <!-- Tabel DataTables -->
                <div class="overflow-x-auto">
                    <table id="reports-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">No</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Deskripsi</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Lokasi</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Sesi</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <!-- Body tabel akan diisi oleh DataTables -->
                    </table>
                </div>
            </div>
        </main>

    </div>

    <!-- Scripts: jQuery, DataTables, dll. -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        // Fungsi untuk meng-update warna teks select berdasarkan value
        function updateSelectColor(selectElem) {
            var value = $(selectElem).val();
            var colorMapping = {
                'pending': '#FBBF24',
                'approved': '#4ADE80',
                'rejected': '#F87171'
            };
            $(selectElem).css('color', colorMapping[value]);
        }

        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#reports-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('getStatus') }}',
                columns: [{
                        data: 'DT_RowIndex', // Mengambil nomor urut dari addIndexColumn()
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'session',
                        name: 'session'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                autoWidth: false,
                responsive: true,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
                },
                order: [
                    [3, 'desc']
                ],
                drawCallback: function() {
                    $('.status-dropdown').each(function() {
                        updateSelectColor(this);
                    });
                }
            });

            // Update warna dan AJAX update saat terjadi perubahan dropdown status
            $(document).on('change', '.status-dropdown', function() {
                updateSelectColor(this);
                var selectElem = $(this);
                var reportId = selectElem.data('id');
                var newStatus = selectElem.val();

                $.ajax({
                    url: '{{ route('updateStatus') }}',
                    type: 'POST',
                    data: {
                        id: reportId,
                        status: newStatus
                    },
                    success: function(response) {
                        // Tampilkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Reload tabel tanpa mereset paging
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        // Tampilkan notifikasi error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memperbarui status.'
                        });
                        table.ajax.reload(null, false);
                    }
                });
            });


            // Handler untuk Approve Semua button
            $(document).on('click', '#approve-all-btn', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Approve Semua',
                    text: 'Apakah Anda yakin ingin mengapprove semua laporan yang pending?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Approve Semua',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4ADE80'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('approveAll') }}',
                            type: 'POST',
                            data: {},
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#4ADE80'
                                });
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat mengapprove semua laporan.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handler untuk mobile sidebar
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

        // Update clock
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
