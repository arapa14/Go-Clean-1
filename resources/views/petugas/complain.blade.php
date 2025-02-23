<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Complain</title>
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
    </style>
</head>

<body class="bg-gray-100">
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

            <form action="{{ route('complain.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div id="image-container">
                    <label for="image-1" class="image-label">
                        <input type="file" name="image[]" id="image-1" accept="image/*" capture="environment"
                            class="image-input" onchange="previewImage(event, 1)">
                        <img id="preview-1" class="image-preview hidden">
                    </label>
                </div>

                <button type="button" onclick="addImageInput()">+</button>

                {{-- Input Jenis Komplain --}}
                <div class="mb-4">
                    <label for="complaint" class="block text-sm font-medium text-gray-700">Jenis Komplain</label>
                    <select name="complaint" id="complaint" class="w-full p-2 border rounded-md" required>
                        <option value="">Pilih Jenis Komplain</option>
                        <option value="missing">Kehilangan</option>
                        <option value="broken">Kerusakan</option>
                        <option value="require">Membutuhkan</option>
                    </select>
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
                    <textarea name="description" id="description" placeholder="Deskripsi komplain Anda" class="w-full p-2 border rounded-md"
                        required></textarea>
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
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Jenis Komplain</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Deskripsi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Lokasi</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End container -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#complaints-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('complainPage') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
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

        // Menghapus pesan notifikasi setelah 5 detik
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

        let imageCount = 1;

        function addImageInput() {
            imageCount++;
            const container = document.getElementById('image-container');

            const label = document.createElement('label');
            label.setAttribute('for', `image-${imageCount}`);
            label.classList.add('image-label');

            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('name', 'image[]');
            input.setAttribute('id', `image-${imageCount}`);
            input.setAttribute('accept', 'image/*');
            input.setAttribute('capture', 'environment');
            input.classList.add('image-input');
            input.setAttribute('onchange', `previewImage(event, ${imageCount})`);

            const preview = document.createElement('img');
            preview.setAttribute('id', `preview-${imageCount}`);
            preview.classList.add('image-preview', 'hidden');

            label.appendChild(input);
            label.appendChild(preview);
            container.appendChild(label);
        }

        function previewImage(event, index) {
            const file = event.target.files[0];
            const preview = document.getElementById(`preview-${index}`);

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>
