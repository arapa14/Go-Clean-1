<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Komplain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        .image-label {
            display: inline-block;
            width: 100px;
            height: 100px;
            background-color: #f3f3f3;
            border: 2px dashed #ccc;
            text-align: center;
            line-height: 100px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .image-input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hidden {
            display: none;
        }

        /* Loading Overlay Style */
        #loadingOverlay {
            position: fixed;
            inset: 0;
            background-color: rgba(243, 244, 246, 0.75);
            /* bg-gray-50 dengan opacity 75% */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        #loadingOverlay.hidden {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
    </div>

    <!-- Wrapper container -->
    <div class="container mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
            <a href="{{ route('dashboard') }}"
                class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 sm:text-lg hover:bg-blue-100 p-2 rounded-xl">Welcome,
                {{ $user->name }}</a>
            <span id="realTimeClock" class="text-gray-600 mb-2 sm:mb-0 sm:text-md">Memuat Waktu...</span>
            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                    class="flex items-center text-red-600 hover:text-red-800 text-lg w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        {{-- Form Komplain --}}
        <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Submit Komplain</h2>
            @if ($errors->any())
                <div id="error-message" class="p-3 mb-4 text-sm text-red-600 bg-red-100 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div id="success-message" class="p-3 mb-4 text-sm text-green-600 bg-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form id="reportForm" action="{{ route('complain.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div id="image-upload-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label for="image-1"
                            class="block border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 transition-all duration-300 p-4 flex flex-col items-center justify-center cursor-pointer"
                            aria-label="Unggah gambar" role="button">
                            <input type="file" name="images[]" id="image-1" accept="image/*" capture="environment"
                                class="hidden" onchange="previewImage(event, 'preview-1')">
                            <div id="default-image-1" class="flex flex-col items-center">
                                <i class="fas fa-camera text-3xl text-gray-500"></i>
                                <p class="mt-2 text-gray-600 text-center">Klik atau tap untuk mengambil gambar / pilih
                                    file</p>
                            </div>
                            <img id="preview-1" src="#" alt="Preview Gambar"
                                class="mt-2 hidden object-cover w-full h-48 rounded-md transition-all duration-300 ease-in-out">
                        </label>
                    </div>
                </div>

                <!-- Tombol untuk menambah input gambar tambahan -->
                <button type="button" onclick="addImageUpload()"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all duration-300">
                    Tambah Gambar
                </button>

                {{-- Input Jenis Komplain --}}
                <div class="mb-4">
                    <label class="block text-lg font-semibold text-gray-700">Jenis Komplain</label>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label
                            class="cursor-pointer flex items-center p-4 border rounded-lg hover:shadow-lg transition duration-200">
                            <input type="radio" name="complaint" value="missing"
                                class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500" required>
                            <span class="ml-3 text-gray-800">Kehilangan</span>
                        </label>
                        <label
                            class="cursor-pointer flex items-center p-4 border rounded-lg hover:shadow-lg transition duration-200">
                            <input type="radio" name="complaint" value="broken"
                                class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500" required>
                            <span class="ml-3 text-gray-800">Kerusakan</span>
                        </label>
                        <label
                            class="cursor-pointer flex items-center p-4 border rounded-lg hover:shadow-lg transition duration-200">
                            <input type="radio" name="complaint" value="require"
                                class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500" required>
                            <span class="ml-3 text-gray-800">Membutuhkan</span>
                        </label>
                    </div>
                </div>


                {{-- Input Lokasi --}}
                <div class="mb-4">
                    <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" name="location" id="location" placeholder="Lokasi kejadian"
                        class="w-full p-2 border rounded-md" required>
                </div>

                {{-- Input Deskripsi --}}
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" placeholder="Deskripsi komplain Anda"
                        class="w-full p-2 border rounded-md" required></textarea>
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        {{-- Riwayat Komplain dengan Yajra DataTable --}}
        <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-white text-2xl font-bold">Riwayat Komplain</h1>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="complaints-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">No</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Jenis Komplain</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Deskripsi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Lokasi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tanggal</th>
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
            <button onclick="closeImagesModal()"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 z-10">
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
    <!-- End container -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        document.getElementById('reportForm').addEventListener('submit', function() {
            // Tampilkan loading overlay
            document.getElementById('loadingOverlay').classList.remove('hidden');
        });

        $(document).ready(function() {
            $('#complaints-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('complainPage') }}',
                columns: [{
                        data: 'DT_RowIndex', // Mengambil nomor urut dari addIndexColumn()
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'complaint',
                        name: 'complaint'
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
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
        // Fungsi updateClock untuk mengambil dan menampilkan waktu server secara real-time
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

        // Hapus notifikasi (error/success) setelah 5 detik
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

        /**
         * Fungsi previewImage()
         * Menampilkan preview gambar saat pengguna memilih file.
         * @param {Event} event - Event onchange dari input file.
         * @param {string} previewId - ID elemen <img> untuk preview.
         */
        function previewImage(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            const defaultContent = input.parentElement.querySelector('div');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    // Sembunyikan konten default (ikon dan instruksi)
                    defaultContent.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /**
         * Fungsi addImageUpload()
         * Menambahkan input gambar baru secara dinamis.
         */
        let imageUploadCount = 1;

        function addImageUpload() {
            imageUploadCount++;
            const container = document.getElementById('image-upload-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <label for="image-${imageUploadCount}" class="block border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 transition-all duration-300 p-4 flex flex-col items-center justify-center cursor-pointer" aria-label="Unggah gambar" role="button">
                    <input type="file" name="images[]" id="image-${imageUploadCount}" accept="image/*" capture="environment" class="hidden" onchange="previewImage(event, 'preview-${imageUploadCount}')">
                    <div id="default-image-${imageUploadCount}" class="flex flex-col items-center">
                        <i class="fas fa-camera text-3xl text-gray-500"></i>
                        <p class="mt-2 text-gray-600 text-center">Klik atau tap untuk mengambil gambar / pilih file</p>
                    </div>
                    <img id="preview-${imageUploadCount}" src="#" alt="Preview Gambar" class="mt-2 hidden object-cover w-full h-48 rounded-md transition-all duration-300 ease-in-out">
                </label>
            `;
            container.appendChild(div);
        }
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
