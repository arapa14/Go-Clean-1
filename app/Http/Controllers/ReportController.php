<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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
        // 1. Buat instance image manager dengan driver GD
        $manager = new ImageManager(new Driver);

        // 2. Tentukan nama file & path penyimpanan
        $uniqueId  = uniqid();
        $extension = $imageFile->getClientOriginalExtension();
        $imageName = $imageNamePrefix . '_' . time() . '_' . $uniqueId . '.' . $extension;
        $imagePath = storage_path("app/public/{$directory}/{$imageName}");;

        // 3. Baca gambar dengan Intervention Image
        $image = $manager->read($imageFile->getRealPath());
        $imgWidth  = $image->width();
        $imgHeight = $image->height();

        // 4. Definisikan batas watermark agar tidak menutupi foto
        //    - Maksimal 40% dari lebar gambar
        //    - Maksimal 20% dari tinggi gambar
        // Silakan sesuaikan persentase ini sesuai kebutuhan desain.
        $maxWatermarkWidth  = 0.4 * $imgWidth;
        $maxWatermarkHeight = 0.2 * $imgHeight;

        // 5. Margin luar (dari tepi gambar) dan padding dalam (di dalam rectangle)
        $margin  = 10;
        $padding = 10;

        // 6. Siapkan teks multi-baris
        $lines = explode("\n", $watermarkText);

        /**
         * 7. Cari ukuran font terbesar yang masih muat dalam batas
         *    Menggunakan pendekatan binary search agar lebih efisien.
         *    - Batas bawah (minFontSize) = 10
         *    - Batas atas (maxFontSize)  = 150 (silakan sesuaikan)
         */
        $minFontSize = 10;
        $maxFontSize = 150;
        $bestFontSize = $minFontSize; // nilai awal

        while ($minFontSize <= $maxFontSize) {
            $mid = (int) floor(($minFontSize + $maxFontSize) / 2);

            // Ukur bounding box teks dengan font size = $mid
            $textBox = $this->measureTextBox($lines, $fontPath, $mid);

            // Tambahkan padding untuk rectangle
            $watermarkW = $textBox['width']  + $padding * 2;
            $watermarkH = $textBox['height'] + $padding * 2;

            // Cek apakah muat dalam batas
            if ($watermarkW <= $maxWatermarkWidth && $watermarkH <= $maxWatermarkHeight) {
                // Masih muat -> perbesar font
                $bestFontSize = $mid;
                $minFontSize = $mid + 1;
            } else {
                // Terlalu besar -> perkecil font
                $maxFontSize = $mid - 1;
            }
        }

        // 8. Hitung ulang ukuran background watermark dengan bestFontSize
        $textBox   = $this->measureTextBox($lines, $fontPath, $bestFontSize);
        $watermarkW = $textBox['width']  + $padding * 2;
        $watermarkH = $textBox['height'] + $padding * 2;

        // 9. Tentukan posisi rectangle di pojok kanan-bawah
        $backgroundX = $imgWidth  - $watermarkW - $margin;
        $backgroundY = $imgHeight - $watermarkH - $margin;

        // 10. Gambar rectangle background watermark (Intervention Image v3.x)
        $image->drawRectangle($backgroundX, $backgroundY, function (RectangleFactory $rectangle) use ($watermarkW, $watermarkH) {
            $rectangle->size($watermarkW, $watermarkH);
            $rectangle->background('rgba(0, 0, 0, 0.5)'); // set semi-transparan
            $rectangle->border('white', 2);
        });

        // 11. Letakkan teks di pojok kanan-bawah rectangle
        $textX = $backgroundX + $watermarkW  - $padding;
        $textY = $backgroundY + $watermarkH - $padding;

        $image->text($watermarkText, $textX, $textY, function ($font) use ($fontPath, $bestFontSize) {
            $font->file($fontPath);
            $font->size($bestFontSize);
            $font->color('rgba(255, 255, 255, 0.9)');
            $font->align('right');
            $font->valign('bottom');
        });

        // 12. Pastikan direktori penyimpanan gambar ada
        if (!file_exists(storage_path("app/public/{$directory}"))) {
            mkdir(storage_path("app/public/{$directory}"), 0755, true);
        }

        // 13. Simpan gambar dengan kualitas awal 80%
        $quality = 80;
        $image->save($imagePath, $quality);

        // 14. Kompres gambar jika ukuran melebihi sizeLimit (dalam KB)
        while (filesize($imagePath) > $sizeLimit * 1024) {
            if ($quality <= 10) {
                break;
            }
            $quality -= 10;
            $image->save($imagePath, $quality);
        }

        // 15. Kembalikan path publik
        return "/{$directory}/{$imageName}";
    }

    /**
     * Helper untuk mengukur total width & height dari teks multi-baris
     * dengan font size tertentu. Memperhitungkan line spacing agar teks
     * tidak terlalu rapat antarbaris.
     */
    private function measureTextBox(array $lines, string $fontPath, int $fontSize): array
    {
        $maxWidth = 0;
        $totalHeight = 0;
        $lineSpacing = 1.2; // spasi antar baris, bisa disesuaikan

        foreach ($lines as $index => $line) {
            // bounding box baris
            $box = imagettfbbox($fontSize, 0, $fontPath, $line);
            $width  = abs($box[2] - $box[0]);
            $height = abs($box[1] - $box[5]); // tinggi bounding box baris

            if ($width > $maxWidth) {
                $maxWidth = $width;
            }
            // Tambahkan tinggi
            $totalHeight += ($index === 0)
                ? $height
                : $height * $lineSpacing;
        }

        return [
            'width'  => $maxWidth,
            'height' => $totalHeight,
        ];
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
        $enable_time_restriction = $settings['enable_time_restriction'] ?? '1';
        $enable_session_restriction = $settings['enable_session_restriction'] ?? '0';

        $pagi_start  = (int) ($settings['pagi_start'] ?? 6);
        $pagi_end    = (int) ($settings['pagi_end'] ?? 12);
        $siang_start = (int) ($settings['siang_start'] ?? 12);
        $siang_end   = (int) ($settings['siang_end'] ?? 15);
        $sore_start  = (int) ($settings['sore_start'] ?? 15);
        $sore_end    = (int) ($settings['sore_end'] ?? 17);

        // Tentukan sesi berdasarkan waktu saat ini
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

        // Cek jika sesi terbatas dan user sudah kirim di sesi ini
        if ($enable_session_restriction) {
            $reportSesiIni = Report::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->where('session', $session)
                ->exists();

            if ($reportSesiIni) {
                return redirect()->back()->withErrors([
                    'error' => "Anda sudah mengirim report sesi {$session} hari ini!"
                ]);
            }
        }

        // Batasi maksimal 3 laporan per hari (bukan per gambar)
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

        // **Validasi Input**
        $request->validate([
            'images'      => 'required|array|min:1', // Pastikan ada minimal 1 gambar
            'images.*'    => 'file|mimes:jpg,png', // 
            'description' => 'required|string|max:255',
            'location'    => 'required|string|not_in:Pilih lokasi',
        ]);

        // Pastikan images selalu dalam bentuk array
        $imageFiles = (array) $request->file('images');

        // **Konfigurasi Watermark & Penyimpanan**
        $imageNamePrefix = 'report';
        $directory = 'image';
        $userName = $user->name ?? 'Guest';
        $watermarkText = $userName . " - " . now()->format('d/m/Y H:i:s');
        $fontPath = public_path('arial.ttf');

        try {
            $imagePaths = []; // Untuk menyimpan path gambar

            foreach ($imageFiles as $imageFile) {
                // Proses gambar dengan fungsi `processImage()`
                $processedImagePath = $this->processImage(
                    $imageFile,
                    $imageNamePrefix,
                    $directory,
                    $watermarkText,
                    $fontPath
                );

                // Simpan path gambar
                $imagePaths[] = $processedImagePath;
            }

            // **Simpan Report** (satu report per banyak gambar)
            $report = new Report();
            $report->user_id     = $user->id;
            $report->name        = $user->name;
            $report->description = $request->input('description');
            $report->location    = $request->input('location');
            $report->date        = now();
            $report->session     = $session;
            $report->status      = 'pending';
            $report->image       = json_encode($imagePaths); // Simpan array gambar sebagai JSON
            $report->save();

            return redirect()->back()->with('success', 'Berhasil mengirim report.');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Gagal mengirim report');
        }
    }


    public function riwayat()
    {
        $user = Auth::user();
        return view('petugas.riwayat', compact('user'));
    }

    // Menghandle AJAX request dari DataTables
    public function getReports(Request $request)
    {
        $userId = Auth::id(); // Hanya report milik user yang sedang login
        $reports = Report::where('user_id', $userId)->orderBy('created_at', 'desc');

        return DataTables::of($reports)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                if ($row->status === 'approved') {
                    return '<span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-semibold uppercase">Approved</span>';
                } elseif ($row->status === 'rejected') {
                    return '<span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold uppercase">Rejected</span>';
                } else {
                    return '<span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-semibold uppercase">Pending</span>';
                }
            })
            ->addColumn('action', function ($row) {
                $images = json_decode($row->image, true);
                $imagesJson = htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8');
                $button = '<button class="action-icon btn-view p-2" onclick="openImagesModal(\'' . $imagesJson . '\')" title="Lihat Semua Gambar">
                            <i class="fa-solid fa-images"></i>
                       </button>';
                return '<div class="flex justify-center">' . $button . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }



    // reviewer and admin

    public function status()
    {
        // Tampilkan halaman status laporan di view petugas.status
        $totalNewComplaints = Complaint::where('status', 'pending')->count();
        return view('reviewer.status', compact('totalNewComplaints'));
    }

    public function getStatus(Request $request)
    {
        // Ambil semua data report, urutkan berdasarkan tanggal terbaru
        $reports = Report::orderBy('created_at', 'desc');

        return DataTables::of($reports)
            ->addIndexColumn() // Menambahkan nomor urut (DT_RowIndex)
            // Kolom status diubah menjadi dropdown
            ->editColumn('status', function ($row) {
                $statuses = [
                    'pending'  => 'pending',
                    'approved' => 'approved',
                    'rejected' => 'rejected'
                ];
                $dropdown = '<select class="status-dropdown border p-1 rounded" data-id="' . $row->id . '">';
                foreach ($statuses as $value => $label) {
                    $selected = $row->status === $value ? 'selected' : '';
                    $style = '';
                    if ($value === 'pending') {
                        $style = 'color: #FBBF24;';
                    } elseif ($value === 'approved') {
                        $style = 'color: #4ADE80;';
                    } elseif ($value === 'rejected') {
                        $style = 'color: #F87171;';
                    }
                    $dropdown .= "<option value='{$value}' style='{$style}' {$selected}>{$label}</option>";
                }
                $dropdown .= '</select>';
                return $dropdown;
            })


            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($row) {
                // Decode JSON untuk mendapatkan array gambar
                $images = json_decode($row->image, true);
                // Ambil gambar pertama, jika ada
                $firstImage = !empty($images) ? $images[0] : '';

                $viewIcon = '<a href="' . asset('storage/' . $firstImage) . '" target="_blank" class="action-icon btn-view" title="Lihat Gambar">
                            <i class="fa-solid fa-eye"></i>
                         </a>';
                $downloadIcon = '<a href="' . asset('storage/' . $firstImage) . '" download class="action-icon btn-download" title="Download Gambar">
                                <i class="fa-solid fa-download"></i>
                             </a>';
                return '<div class="flex justify-center gap-2">' . $viewIcon . $downloadIcon . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    // Method untuk meng-update status via AJAX
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:reports,id',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $report = Report::find($request->id);
        $report->status = $request->status;
        $report->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    public function approveAll(Request $request)
    {
        // Update semua laporan yang masih pending menjadi approved
        $updated = Report::where('status', 'pending')->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Semua laporan berhasil di-approve. Total updated: ' . $updated
        ]);
    }
}
