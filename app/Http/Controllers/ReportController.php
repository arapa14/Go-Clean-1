<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    private function processImage($imageFile, $imageNamePrefix, $directory, $watermarkText, $fontPath, $sizeLimit = 1024)
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // Buat nama file gambar
        $imageName = time() . "_{$imageNamePrefix}." . $imageFile->getClientOriginalExtension();

        // Tentukan path lengkap untuk menyimpan gambar (di folder storage/app/public/{$directory})
        $imagePath = storage_path("app/public/{$directory}/{$imageName}");

        // Buat instance gambar menggunakan Intervention Image
        $image = $manager->read($imageFile->getRealPath());

        // --- Konfigurasi Watermark ---
        $padding = 30;
        $fontSize = 40;

        // Pisahkan teks watermark ke baris-baris
        $lines = explode("\n", $watermarkText);
        $lineCount = count($lines);
        $lineHeight = $fontSize + 5;
        $textBoxHeight = $lineCount * $lineHeight;

        // Hitung lebar teks terpanjang menggunakan fungsi imagettfbbox
        $maxTextWidth = 0;
        foreach ($lines as $line) {
            $box = imagettfbbox($fontSize, 0, $fontPath, $line);
            $textWidth = abs($box[2] - $box[0]);
            if ($textWidth > $maxTextWidth) {
                $maxTextWidth = $textWidth;
            }
        }
        $textBoxWidth = $maxTextWidth;

        // Tentukan ukuran background watermark
        $backgroundWidth = $textBoxWidth - 10;
        $backgroundHeight = $textBoxHeight + $padding * 2;

        // Tentukan margin dari tepi gambar
        $margin = 10;

        // Hitung koordinat background (pojok kiri atas)
        $backgroundX = $image->width() - $backgroundWidth - $margin;
        $backgroundY = $image->height() - $backgroundHeight - $margin;

        // Tambahkan background semi-transparan di pojok kanan bawah
        $image->drawRectangle($backgroundX, $backgroundY, function (RectangleFactory $rectangle) use ($backgroundWidth, $backgroundHeight) {
            $rectangle->size($backgroundWidth, $backgroundHeight);
            $rectangle->background('rgba(0, 0, 0, 0.5)');
            $rectangle->border('white', 2);
        });

        // Tentukan posisi teks: ditempatkan dengan align kanan dan bawah di dalam background
        $textX = $backgroundX + $backgroundWidth - $padding;
        $textY = $backgroundY + $backgroundHeight - $padding;

        // Tambahkan teks watermark
        $image->text($watermarkText, $textX, $textY, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color('rgba(255, 255, 255, 0.9)');
            $font->align('right');
            $font->valign('bottom');
        });


        // Pastikan direktori penyimpanan gambar sudah ada
        if (!file_exists(storage_path("app/public/{$directory}"))) {
            mkdir(storage_path("app/public/{$directory}"), 0755, true);
        }

        // Simpan gambar dengan kualitas awal 80%
        $quality = 80;
        $image->save($imagePath, $quality);

        // Lakukan kompresi tambahan jika ukuran file melebihi batas ($sizeLimit dalam KB)
        while (filesize($imagePath) > $sizeLimit * 1024) {
            // Jika kualitas sudah terlalu rendah, hentikan loop
            if ($quality <= 10) {
                break;
            }
            $quality -= 10;
            $image->save($imagePath, $quality);
        }

        // Kembalikan path gambar yang bisa diakses secara publik
        // (pastikan Anda telah menjalankan "php artisan storage:link" untuk membuat symbolic link ke folder storage)
        return "/{$directory}/{$imageName}";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentHour = Carbon::now()->hour;

        // Ambil semua settings sebagai array key-value
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Konfigurasi dinamis dari settings
        // Jika tidak ditemukan, gunakan default value
        $enable_time_restriction = isset($settings['enable_time_restriction'])
            ? ($settings['enable_time_restriction'] == '1')
            : true;

        $enable_session_restriction = isset($settings['enable_session_restriction'])
            ? ($settings['enable_session_restriction'] == '1')
            : false; // default false jika tidak di-set

        $pagi_start  = isset($settings['pagi_start'])  ? (int)$settings['pagi_start']  : 6;
        $pagi_end    = isset($settings['pagi_end'])    ? (int)$settings['pagi_end']    : 12;
        $siang_start = isset($settings['siang_start']) ? (int)$settings['siang_start'] : 12;
        $siang_end   = isset($settings['siang_end'])   ? (int)$settings['siang_end']   : 15;
        $sore_start  = isset($settings['sore_start'])  ? (int)$settings['sore_start']  : 15;
        $sore_end    = isset($settings['sore_end'])    ? (int)$settings['sore_end']    : 17;

        // Tentukan sesi berdasarkan waktu saat ini menggunakan setting
        if ($currentHour >= $pagi_start && $currentHour < $pagi_end) {
            $session = 'pagi';
        } elseif ($currentHour >= $siang_start && $currentHour < $siang_end) {
            $session = 'siang';
        } elseif ($currentHour >= $sore_start && $currentHour < $sore_end) {
            $session = 'sore';
        } else {
            if ($enable_session_restriction) {
                return redirect()->back()->withErrors([
                    'error' => "Report hanya dapat dikirim antara {$pagi_start}:00 hingga {$sore_end}:00!"
                ]);
            }
            $session = 'invalid';
        }

        // Jika pembatasan sesi diaktifkan, cek apakah user sudah mengirim report pada sesi ini hari ini
        if ($enable_session_restriction) {
            $reportSesiIni = Report::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->where('time', $session)
                ->exists();

            if ($reportSesiIni) {
                return redirect()->back()->withErrors([
                    'error' => "Anda sudah mengirim report sesi {$session} hari ini!"
                ]);
            }
        }

        // Jika pembatasan jumlah upload diaktifkan, hitung report hari ini dan batasi maksimal 3 report per hari
        if ($enable_time_restriction) {
            $jumlahReportHariIni = Report::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();

            if ($jumlahReportHariIni >= 3) {
                return redirect()->back()->withErrors([
                    'error' => 'Anda telah mencapai batas maksimal 3 report hari ini!'
                ]);
            }
        }

        // Validasi input dari form
        $request->validate([
            'images'      => 'required|file|mimes:jpg,png|max:4096',
            'description' => 'required|string|max:255',
            'location'    => 'required|string|not_in:Pilih lokasi',
        ]);

        // Ambil file gambar yang diupload
        $imageFile = $request->file('images');

        // Nama prefix untuk gambar, misalnya "report"
        $imageNamePrefix = 'report';

        // Direktori penyimpanan gambar (misalnya: storage/app/public/report_images)
        $directory = 'image';

        // Watermark: gabungan nama user dan timestamp
        $userName = auth()->check() ? auth()->user()->name : 'Guest';
        $watermarkText = $userName . " - " . now()->format('d/m/Y H:i:s');

        // Path file font (sesuaikan dengan lokasi file font Anda)
        $fontPath = public_path('arial.ttf');

        // Proses gambar dan dapatkan path gambar yang telah diproses
        $processedImagePath = $this->processImage($imageFile, $imageNamePrefix, $directory, $watermarkText, $fontPath);

        try {
            // Menyimpan report ke database
            $report = new Report();
            $report->user_id     = Auth::id();
            $report->name        = Auth::user()->name;
            $report->description = $request->input('description');
            $report->location    = $request->input('location');
            $report->date        = now();
            $report->session        = $session;
            $report->status      = 'pending';
            $report->image       = $processedImagePath;
            $report->save();

            return redirect()->back()->with('success', 'Berhasil mengirim report.');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Gagal mengirim report');
        }
    }

    // Menampilkan halaman index report
    public function riwayat()
    {
        $user = Auth::user();
        return view('petugas-kebersihan.riwayat', compact('user'));
    }

    // Menghandle AJAX request dari DataTables
    public function getReports(Request $request)
    {
        $userId = Auth::id(); // Hanya report milik user yang sedang login
        $reports = Report::where('user_id', $userId);

        return DataTables::of($reports)
            // Ubah kolom status untuk menampilkan badge dengan warna berbeda
            ->editColumn('status', function ($row) {
                if ($row->status === 'approved') {
                    return '<span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-semibold uppercase">Approved</span>';
                } elseif ($row->status === 'rejected') {
                    return '<span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold uppercase">Rejected</span>';
                } else { // pending
                    return '<span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-semibold uppercase">Pending</span>';
                }
            })
            ->addColumn('action', function ($row) {
                // Icon untuk melihat gambar
                $viewIcon = '<a href="' . asset('storage/' . $row->image) . '" target="_blank" class="action-icon btn-view" title="Lihat Gambar">
                           <i class="fa-solid fa-eye"></i>
                         </a>';
                // Icon untuk mendownload gambar
                $downloadIcon = '<a href="' . asset('storage/' . $row->image) . '" download class="action-icon btn-download" title="Download Gambar">
                               <i class="fa-solid fa-download"></i>
                             </a>';
                return '<div class="flex justify-center gap-2">' . $viewIcon . $downloadIcon . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}
