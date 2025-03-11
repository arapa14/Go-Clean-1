<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Laporan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom styling untuk DataTables agar selaras dengan Tailwind */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #e5e7eb;
            /* bg-gray-200 */
            color: #374151 !important;
            /* text-gray-700 */
            margin: 0 0.125rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #d1d5db;
            /* bg-gray-300 */
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            outline: none;
        }

        table.dataTable thead {
            background-color: #f3f4f6;
            /* bg-gray-100 */
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
            /* blue-500 */
            color: #ffffff;
        }

        .btn-download {
            background-color: #10b981;
            /* green-500 */
            color: #ffffff;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-lg rounded-lg p-5 flex flex-wrap justify-between items-center gap-4">
        <!-- Username -->
        <h1 class="text-2xl font-semibold text-blue-700">Welcome, {{ $user->name }}</h1>
        <div class="flex items-center gap-3">
            <!-- Tombol Kembali ke Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-gradient-to-r from-blue-700 to-blue-900 transition duration-300">
                Back to Dashboard
            </a>
            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center text-red-600 hover:text-red-800 font-semibold transition duration-300">
                    <!-- Ikon Logout -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-white text-2xl font-bold">Daftar Report</h1>
            </div>
            <!-- Tabel -->
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="reports-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">No</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nama</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Deskripsi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Lokasi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Sesi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Gambar -->
    <div id="imagesModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeImagesModalOnOverlay(event)">
        <div class="bg-white p-4 rounded-lg max-w-3xl w-full mx-4 relative max-h-screen overflow-y-auto">
            <!-- Tombol Close (selalu terlihat di pojok kanan atas) -->
            <button onclick="closeImagesModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 z-10">
                <i class="fa-solid fa-xmark fa-2x"></i>
            </button>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-center">Detail Gambar</h2>
            </div>
            <div id="modalImagesContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Thumbnail gambar akan dimuat di sini -->
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reports-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('report.getReports') }}',
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'session',
                        name: 'session'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
                }
            });
        });
    </script>

    <script>
        // Fungsi untuk membuka modal dan memuat gambar-gambar
        function openImagesModal(imagesJson) {
            const images = JSON.parse(imagesJson);
            const container = document.getElementById('modalImagesContainer');
            container.innerHTML = ''; // Bersihkan kontainer

            images.forEach(function(image) {
                const imageUrl = '{{ asset('storage') }}/' + image;
                const imageDiv = document.createElement('div');
                imageDiv.classList.add('flex', 'flex-col', 'items-center', 'gap-2', 'border', 'p-2', 'rounded',
                    'shadow-sm');

                const imgEl = document.createElement('img');
                imgEl.src = imageUrl;
                imgEl.alt = "Report Image";
                imgEl.classList.add('object-contain', 'w-full', 'h-48');

                const downloadLink = document.createElement('a');
                downloadLink.href = imageUrl;
                downloadLink.download = '';
                downloadLink.classList.add('action-icon', 'btn-download', 'p-2', 'rounded-full');
                downloadLink.title = "Download Gambar";
                downloadLink.innerHTML = '<i class="fa-solid fa-download"></i>';

                imageDiv.appendChild(imgEl);
                imageDiv.appendChild(downloadLink);
                container.appendChild(imageDiv);
            });

            // Tampilkan modal
            document.getElementById('imagesModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeImagesModal() {
            document.getElementById('imagesModal').classList.add('hidden');
        }

        // Jika klik di luar konten modal (overlay), maka tutup modal
        function closeImagesModalOnOverlay(event) {
            // Pastikan hanya ketika yang diklik adalah overlay (bukan konten modal)
            if (event.target.id === 'imagesModal') {
                closeImagesModal();
            }
        }
    </script>


</body>

</html>
