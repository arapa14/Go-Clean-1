<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Yajra\DataTables\Facades\DataTables;

class ComplaintController extends Controller
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

        // Validasi input dari form
        $request->validate([
            'images'      => 'required|array',
            'images.*'    => 'file|mimes:jpg,png|max:4096',
            'complaint'  => 'required|string',
            'description' => 'required|string|max:255',
            'location'   => 'required|string|not_in:Pilih lokasi',
        ]);

        // Nama prefix untuk gambar
        $imageNamePrefix = 'complain';

        // Direktori penyimpanan gambar
        $directory = 'image';

        // Pastikan direktori ada
        if (!Storage::exists("public/$directory")) {
            Storage::makeDirectory("public/$directory");
        }

        // Watermark: nama user + timestamp
        $userName = auth()->check() ? auth()->user()->name : 'Guest';
        $watermarkText = $userName . " - " . now()->format('d/m/Y H:i:s');

        // Path font (sesuaikan dengan lokasi font di proyek Anda)
        $fontPath = public_path('arial.ttf');

        $imagePaths = [];
        foreach ($request->file('images') as $imageFile) {
            $imagePaths[] = $this->processImage($imageFile, $imageNamePrefix, $directory, $watermarkText, $fontPath);
        }

        try {
            // Simpan data complaint ke database
            $complaint = new Complaint();
            $complaint->user_id     = Auth::id();
            $complaint->name        = Auth::user()->name;
            $complaint->description = $request->input('description');
            $complaint->location    = $request->input('location');
            $complaint->complaint   = $request->input('complaint');
            $complaint->image       = json_encode($imagePaths); // Simpan dalam format JSON jika multiple
            $complaint->status      = 'pending';
            $complaint->save();

            return redirect()->back()->with('success', 'Berhasil mengirim complaint.');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Gagal mengirim complaint');
        }
    }

    public function complainPage(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $complaints = Complaint::where('user_id', $user->id)->orderBy('created_at', 'desc');
            return datatables()->of($complaints)
                ->addIndexColumn() // Menambahkan nomor urut (DT_RowIndex)
                ->addColumn('action', function ($row) {
                    $images = json_decode($row->image, true);
                    $imagesJson = htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8');
                    $button = '<button class="action-icon btn-view p-2" onclick="openImagesModal(\'' . $imagesJson . '\')" title="Lihat Semua Gambar">
                                <i class="fa-solid fa-images"></i>
                           </button>';
                    return '<div class="flex justify-center">' . $button . '</div>';
                })
                ->editColumn('created_at', function ($complaint) {
                    return $complaint->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Tampilkan view complain yang berisi form dan DataTable riwayat
        return view('petugas.complain', compact('user'));
    }


    // reviewer and admin
    public function complaint()
    {
        $totalNewComplaints = Complaint::where('status', 'pending')->count();
        $totalPendingReports = Report::where('status', 'pending')->count();
        return view('reviewer.complaint', compact('totalNewComplaints', 'totalPendingReports'));
    }

    public function getComplaint(Request $request)
    {
        // Ambil semua data report, urutkan berdasarkan tanggal terbaru
        $reports = Complaint::orderBy('created_at', 'desc');

        return DataTables::of($reports)
            ->addIndexColumn() // Menambahkan nomor urut (DT_RowIndex)
            ->editColumn('status', function ($row) {
                $statuses = [
                    'pending'      => 'pending',
                    'noted'        => 'noted',
                    'followed-up'  => 'followed-up'
                ];
                $dropdown = '<select class="complaint-status-dropdown border p-1 rounded" data-id="' . $row->id . '">';
                foreach ($statuses as $value => $label) {
                    $selected = $row->status === $value ? 'selected' : '';
                    $style = '';
                    if ($value === 'pending') {
                        $style = 'color: #FBBF24;';
                    } elseif ($value === 'noted') {
                        $style = 'color: #3B82F6;';
                    } elseif ($value === 'followed-up') {
                        $style = 'color: #4ADE80;';
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

    // Method untuk meng-update status complaint via AJAX
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:complaints,id',
            'status' => 'required|in:pending,noted,followed-up'
        ]);

        $complaint = Complaint::find($request->id);
        $complaint->status = $request->status;
        $complaint->save();

        return response()->json(['success' => true, 'message' => 'Complaint status updated successfully.']);
    }

    // Method untuk mengubah semua complaint yang statusnya pending menjadi noted
    public function markAllNoted(Request $request)
    {
        $updated = Complaint::where('status', 'pending')->update(['status' => 'noted']);

        return response()->json([
            'success' => true,
            'message' => 'All pending complaints have been marked as noted. Total updated: ' . $updated
        ]);
    }
}
