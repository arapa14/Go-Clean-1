<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome CSS for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Custom styles untuk ikon */
        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 9999px;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .icon-btn:hover {
            transform: scale(1.1);
        }

        .icon-logout {
            width: 1.5rem;
            height: 1.5rem;
        }

        /* Style untuk tombol navigasi */
        .btn-primary {
            background-color: #2563eb;
            /* blue-600 */
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            /* blue-700 */
        }

        .btn-secondary {
            background-color: #16a34a;
            /* green-600 */
            color: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #15803d;
            /* green-700 */
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
    @if (session()->has('original_user_id'))
        <div id="switch-back-notification" class="fixed top-4 right-4 z-50 pointer-events-none">
            <div
                class="pointer-events-auto flex flex-col items-center px-4 py-3 rounded-lg bg-blue-500 bg-opacity-80 backdrop-blur-sm shadow-lg border border-blue-200">
                <p class="text-sm font-semibold text-white">Mode Impersonasi Aktif</p>
                <a href="{{ route('switchBack') }}"
                    class="mt-1 inline-block text-xs font-medium text-white underline hover:text-white">
                    Kembali ke Akun Admin
                </a>
            </div>
        </div>
    @endif



    <!-- Container Utama -->
    <div class="container mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
            <!-- Nama Pengguna -->
            <h1 class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 sm:text-lg">
                Welcome, {{ $user->name }}
            </h1>

            <!-- Jam (Real-time) -->
            <span id="realTimeClock" class="text-gray-600 mb-2 sm:mb-0 sm:text-md">
                Memuat Waktu...
            </span>

            <!-- Tombol Logout dengan Ikon -->
            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                    class="flex items-center text-red-600 hover:text-red-800 text-lg w-full sm:w-auto">
                    <!-- Ikon Logout -->
                    <!-- Ikon Logout -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        {{-- Tombol Navigasi --}}
        <div class="flex flex-col sm:flex-row gap-4 mt-6">
            <a href="{{ route('riwayat') }}"
                class="btn-primary text-white px-6 py-2 rounded-lg w-full sm:w-auto text-center">
                Lihat Riwayat
            </a>
            <a href="{{ route('complainPage') }}"
                class="btn-secondary text-white px-6 py-2 rounded-lg w-full sm:w-auto text-center">
                Buat Komplain
            </a>
        </div>

        {{-- Form Laporan --}}
        <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Submit Laporan</h2>
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

            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Input Upload Gambar -->
                <div id="image-container">
                    <label for="image-1" class="image-label">
                        <input type="file" name="images[]" id="image-1" accept="image/*" capture="environment"
                            class="image-input" onchange="previewImage(event, 1)">
                        <img id="preview-1" class="image-preview hidden">
                    </label>
                </div>

                <button type="button" onclick="addImageInput()">+</button>

                {{-- Input Lokasi --}}
                <div class="mt-4">
                    <select name="location" class="w-full p-2 border rounded-md">
                        <option>Pilih lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input Deskripsi --}}
                <div class="mt-4">
                    <input type="text" name="description" placeholder="Deskripsi"
                        class="w-full p-2 border rounded-md">
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        {{-- Riwayat Laporan --}}
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

                    <h3 class="mt-4 text-xl font-semibold text-gray-700">
                        Belum Ada Laporan Hari Ini
                    </h3>
                    <p class="text-gray-500 text-sm text-center max-w-sm mt-1">
                        Saat ini belum ada laporan yang masuk. Silakan submit laporan pertama Anda untuk hari ini.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($reportToday as $report)
                        <div class="bg-white shadow-lg rounded-xl border flex flex-col">
                            <!-- Bagian Gambar -->
                            <div
                                class="relative w-full h-48 bg-gray-200 rounded-t-xl overflow-hidden flex items-center justify-center">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="Report Image"
                                    class="object-contain w-full h-full" />
                            </div>

                            <!-- Bagian Konten -->
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-semibold text-blue-600 text-lg mb-1">
                                    {{ ucfirst($report->name) }}
                                </h3>
                                <p class="text-gray-500 text-sm">
                                    <span class="text-blue-500 font-medium">Laporan : </span>
                                    <span class="text-blue-500 font-medium bg-blue-100 px-2 py-1 rounded-md">
                                        {{ $report->session }}
                                    </span>
                                </p>
                                <p class="text-gray-700 mt-2">
                                    {{ $report->description }}
                                </p>

                                <!-- Bagian Bawah Card (lokasi & status) -->
                                <div class="mt-auto flex justify-between items-center text-sm pt-4">
                                    <span class="text-gray-400 font-medium">
                                        {{ $report->location ?? 'Tidak Diketahui' }}
                                    </span>
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
    </div>
    <!-- End Container -->

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

        // Hapus notifikasi setelah 5 detik
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

    {{-- Jika password default, munculkan modal --}}
    @if ($shouldChangePassword)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Buat Password Baru',
                    html: '<input type="password" id="new_password" class="swal2-input" placeholder="Password Baru">' +
                        '<input type="password" id="confirm_password" class="swal2-input" placeholder="Konfirmasi Password">',
                    icon: 'warning',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: false,
                    confirmButtonText: 'Simpan',
                    preConfirm: () => {
                        const password = document.getElementById('new_password').value;
                        const confirm = document.getElementById('confirm_password').value;
                        if (!password || password.length < 6) {
                            Swal.showValidationMessage('Password minimal 6 karakter');
                        } else if (password !== confirm) {
                            Swal.showValidationMessage('Konfirmasi password tidak cocok');
                        }
                        return {
                            password: password
                        };
                    }
                }).then((result) => {
                    if (result.value) {
                        fetch('{{ route('update.password') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    password: result.value.password,
                                    password_confirmation: result.value.password
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.success,
                                        icon: 'success',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else if (data.error) {
                                    Swal.fire('Error', data.error, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error', 'Gagal mengupdate password', 'error');
                            });
                    }
                });
            });
        </script>
    @endif

</body>

</html>
