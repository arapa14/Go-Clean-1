<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Manage Locations</title>
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
        <h1 class="text-xl font-bold text-blue-600">Manage Location</h1>
        <button id="mobile-menu-button" class="text-blue-600 focus:outline-none">
            <i class="fa-solid fa-bars fa-2x"></i>
        </button>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar (gunakan layout yang sama dengan halaman user) -->
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
                    <!-- Badge untuk totalNewComplaints -->
                    <span
                        class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                        {{ $totalNewComplaints }}
                    </span>
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

        <!-- Overlay untuk Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40 sm:hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 ml-0 sm:ml-64 p-4 sm:p-6 relative z-106">
            <div class="mt-6 bg-white shadow-md rounded-lg">
                <div
                    class="bg-blue-600 shadow-md rounded-t-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h1 class="text-2xl font-bold text-white">Manage Locations</h1>
                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                        <button id="addLocationButton"
                            class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-md transition duration-300 shadow">
                            <i class="fas fa-plus"></i>
                            <span>Add Location</span>
                        </button>
                        <span id="realTimeClock" class="bg-white/20 text-white px-3 py-1 rounded-md font-medium">
                            Memuat Waktu...
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table id="locations-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">No</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Location</th>
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data akan di-load lewat AJAX oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

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
        // Update real time clock (memanggil endpoint /server-time)
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
        // Inisialisasi DataTables untuk Locations
        var locationTable = $('#locations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('getLocations') }}',
            columns: [{
                    data: 'DT_RowIndex', // Mengambil nomor urut dari addIndexColumn()
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            autoWidth: false,
            responsive: true,
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
            }
        });
    </script>

    <script>
        // CSRF token untuk fetch request
        const csrfToken = '{{ csrf_token() }}';

        // Create Location: Tampilkan modal untuk menambah location baru
        document.getElementById('addLocationButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Create Location',
                html: `<input id="swal-input1" class="swal2-input" placeholder="Location">`,
                focusConfirm: false,
                showCancelButton: true,
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                },
                preConfirm: () => {
                    return {
                        location: document.getElementById('swal-input1').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        location
                    } = result.value;
                    fetch("{{ route('location.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                location
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Success!",
                                    text: "Location created successfully!",
                                    icon: "success",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                }).then(() => locationTable.ajax.reload());
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: data.error,
                                    icon: "error",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Error!",
                                text: error,
                                icon: "error",
                                customClass: {
                                    confirmButton: 'swal2-confirm'
                                }
                            });
                        });
                }
            });
        });

        // Edit Location: Tampilkan modal edit dan update data location
        function editLocation(button) {
            const locationData = JSON.parse(button.getAttribute('data-location'));
            Swal.fire({
                title: 'Edit Location',
                html: `<input id="swal-input1" class="swal2-input" placeholder="Location" value="${locationData.location}">`,
                focusConfirm: false,
                showCancelButton: true,
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                },
                preConfirm: () => {
                    return {
                        location: document.getElementById('swal-input1').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        location
                    } = result.value;
                    fetch(`/location/${locationData.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                location
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Updated!",
                                    text: "Location has been updated.",
                                    icon: "success",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                }).then(() => locationTable.ajax.reload());
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: data.error,
                                    icon: "error",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred.",
                                icon: "error",
                                customClass: {
                                    confirmButton: 'swal2-confirm'
                                }
                            });
                        });
                }
            });
        }

        // Delete Location: Menghapus location dengan konfirmasi SweetAlert
        function deleteLocation(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/location/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Location has been deleted.",
                                    icon: "success",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                }).then(() => locationTable.ajax.reload());
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: data.error,
                                    icon: "error",
                                    customClass: {
                                        confirmButton: 'swal2-confirm'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred.",
                                icon: "error",
                                customClass: {
                                    confirmButton: 'swal2-confirm'
                                }
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>
