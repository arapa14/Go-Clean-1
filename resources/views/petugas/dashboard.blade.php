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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        /* Style untuk tombol hapus preview (silang) */
        .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
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

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
    </div>

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

            <form id="reportForm" action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Input Upload Gambar -->
                <div id="image-upload-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label for="image-1"
                            class="relative border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 transition-all duration-300 p-4 flex flex-col items-center justify-center cursor-pointer"
                            aria-label="Unggah gambar" role="button">
                            <input type="file" name="images[]" id="image-1" accept="image/*" capture="environment"
                                class="hidden" onchange="previewImage(event, 'preview-1')">
                            <div id="default-image-1" class="flex flex-col items-center">
                                <i class="fas fa-camera text-3xl text-gray-500"></i>
                                <p class="mt-2 text-gray-600 text-center">Klik atau tap untuk mengambil gambar</p>
                            </div>
                            <img id="preview-1" src="#" alt="Preview Gambar"
                                class="mt-2 hidden object-cover w-full h-48 rounded-md transition-all duration-300 ease-in-out">
                            <!-- Tombol hapus preview -->
                            <button type="button" class="remove-preview hidden"
                                onclick="removePreview(event, 'image-1', 'preview-1', 'default-image-1')"><i
                                    class="fa fa-times"></i></button>
                        </label>
                    </div>
                </div>

                <!-- Tombol untuk menambah input gambar tambahan -->
                <button type="button" onclick="addImageUpload()"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all duration-300">
                    Tambah Gambar
                </button>

                {{-- Input Lokasi --}}
                <div class="mt-4">
                    <select name="location" class="w-full p-2 border rounded-md select2">
                        <option value="">Pilih lokasi</option>
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
                            <div class="p-2">
                                @php
                                    $images = json_decode($report->image);
                                    $columns = count($images) > 1 ? 2 : 1;
                                @endphp
                                <div class="grid grid-cols-{{ $columns }} gap-2">
                                    @foreach ($images as $image)
                                        <div
                                            class="bg-gray-200 rounded overflow-hidden h-48 flex items-center justify-center">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Report Image"
                                                class="object-contain max-w-full max-h-full">
                                        </div>
                                    @endforeach
                                </div>
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
                                    @elseif($report->status == 'rejected')
                                        bg-red-100 text-red-600
                                    @else
                                        bg-yellow-100 text-yellow-600 @endif">
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
        document.getElementById('reportForm').addEventListener('submit', function() {
            // Tampilkan loading overlay
            document.getElementById('loadingOverlay').classList.remove('hidden');
        });




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
                    defaultContent.classList.add('hidden');
                    // Tampilkan tombol hapus preview
                    const removeBtn = input.parentElement.querySelector('.remove-preview');
                    if (removeBtn) {
                        removeBtn.classList.remove('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /**
         * Fungsi removePreview()
         * Menghapus preview gambar dan mereset input file.
         * @param {Event} event - Event klik pada tombol remove.
         * @param {string} inputId - ID input file.
         * @param {string} previewId - ID elemen preview image.
         * @param {string} defaultId - ID elemen default (ikon & instruksi).
         */
        function removePreview(event, inputId, previewId, defaultId) {
            event.stopPropagation();
            event.preventDefault();
            // Cari elemen terdekat yang membungkus label upload
            const uploadWrapper = event.currentTarget.closest('div');
            if (uploadWrapper) {
                uploadWrapper.remove();
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
    <label for="image-${imageUploadCount}" class="relative block border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 transition-all duration-300 p-4 flex flex-col items-center justify-center cursor-pointer" aria-label="Unggah gambar" role="button">
      <input type="file" name="images[]" id="image-${imageUploadCount}" accept="image/*" capture="environment" class="hidden" onchange="previewImage(event, 'preview-${imageUploadCount}')">
      <div id="default-image-${imageUploadCount}" class="flex flex-col items-center">
        <i class="fas fa-camera text-3xl text-gray-500"></i>
        <p class="mt-2 text-gray-600 text-center">Klik atau tap untuk mengambil gambar</p>
      </div>
      <img id="preview-${imageUploadCount}" src="#" alt="Preview Gambar" class="mt-2 hidden object-cover w-full h-48 rounded-md transition-all duration-300 ease-in-out">
      <button type="button" class="remove-preview hidden" onclick="removePreview(event, 'image-${imageUploadCount}', 'preview-${imageUploadCount}', 'default-image-${imageUploadCount}')"><i class="fa fa-times"></i></button>
    </label>
  `;
            container.appendChild(div);
        }
    </script>

    {{-- Jika password default, munculkan modal --}}
    @if ($shouldChangePassword)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Atur lebar SweetAlert secara responsif
                const swalWidth = window.innerWidth < 768 ? '90%' : '400px';

                Swal.fire({
                    title: 'Buat Password Baru',
                    icon: 'warning',
                    width: swalWidth,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: false,
                    confirmButtonText: 'Simpan',
                    // HTML untuk menampilkan dua input + toggle password
                    html: `
                <div style="max-width: 300px; margin: 0 auto;">
                    <!-- Input Password Baru -->
                    <div style="position: relative; margin-bottom: 15px;">
                        <input
                            type="password"
                            id="new_password"
                            class="swal2-input"
                            placeholder="Password Baru"
                            style="
                                margin: 0;
                                width: 100%;
                                padding-right: 40px;
                                box-sizing: border-box;
                            "
                        >
                        <span
                            id="toggleNewPassword"
                            style="
                                position: absolute;
                                right: 10px;
                                top: 50%;
                                transform: translateY(-50%);
                                cursor: pointer;
                            "
                        >
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="confirm_password"
                            class="swal2-input"
                            placeholder="Konfirmasi Password"
                            style="
                                margin: 0;
                                width: 100%;
                                padding-right: 40px;
                                box-sizing: border-box;
                            "
                        >
                        <span
                            id="toggleConfirmPassword"
                            style="
                                position: absolute;
                                right: 10px;
                                top: 50%;
                                transform: translateY(-50%);
                                cursor: pointer;
                            "
                        >
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
            `,
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
                    },
                    // didOpen: jalankan setelah SweetAlert ditampilkan, untuk pasang event toggle
                    didOpen: () => {
                        const newPasswordInput = document.getElementById('new_password');
                        const confirmPasswordInput = document.getElementById('confirm_password');
                        const toggleNewPassword = document.getElementById('toggleNewPassword');
                        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

                        // Fungsi toggle "lihat/sembunyikan" password baru
                        toggleNewPassword.addEventListener('click', function() {
                            if (newPasswordInput.type === 'password') {
                                newPasswordInput.type = 'text';
                                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                            } else {
                                newPasswordInput.type = 'password';
                                this.innerHTML = '<i class="fa fa-eye"></i>';
                            }
                        });

                        // Fungsi toggle "lihat/sembunyikan" konfirmasi password
                        toggleConfirmPassword.addEventListener('click', function() {
                            if (confirmPasswordInput.type === 'password') {
                                confirmPasswordInput.type = 'text';
                                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                            } else {
                                confirmPasswordInput.type = 'password';
                                this.innerHTML = '<i class="fa fa-eye"></i>';
                            }
                        });
                    }
                }).then((result) => {
                    // Jika valid, kirim data ke server
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




    <!-- jQuery (jika belum disertakan) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih lokasi",
                allowClear: true
            });
        });
    </script>


</body>

</html>
