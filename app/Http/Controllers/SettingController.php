<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting; // Asumsikan Anda punya model Setting
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman settings
     */
    public function index()
    {
        $logo = Setting::where('key', 'logo')->first()->value;
        $settings = Setting::pluck('value', 'key')->toArray();
        $totalNewComplaints = Complaint::where('status', 'pending')->count();
        return view('admin.settings', compact(['settings', 'logo', 'totalNewComplaints']));
    }

    /**
     * Memproses form update settings
     */
    public function update(Request $request)
    {
        // Buat validasi
        $rules = [
            'enable_time_restriction' => 'sometimes|required|in:0,1',
            'pagi_start' => 'sometimes|required|min:0|max:23',
            'pagi_end'   => 'sometimes|required|min:0|max:23',
            'siang_start' => 'sometimes|required|min:0|max:23',
            'siang_end'  => 'sometimes|required|min:0|max:23',
            'sore_start' => 'sometimes|required|min:0|max:23',
            'sore_end'   => 'sometimes|required|min:0|max:23',
            'nama_sistem' => 'sometimes|nullable|string|max:255',
            'logo_sistem' => 'sometimes|nullable|image',
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->filled('enable_time_restriction')) {
            Setting::where('key', 'enable_time_restriction')
                ->update(['value' => $request->enable_time_restriction]);
        }

        if ($request->filled('enable_session_restriction')) {
            Setting::where('key', 'enable_session_restriction')
                ->update(['value' => $request->enable_session_restriction]);
        }

        if ($request->filled('pagi_start')) {
            Setting::where('key', 'pagi_start')
                ->update(['value' => $request->pagi_start]);
        }

        if ($request->filled('pagi_end')) {
            Setting::where('key', 'pagi_end')
                ->update(['value' => $request->pagi_end]);
        }

        if ($request->filled('siang_start')) {
            Setting::where('key', 'siang_start')
                ->update(['value' => $request->siang_start]);
        }

        if ($request->filled('siang_end')) {
            Setting::where('key', 'siang_end')
                ->update(['value' => $request->siang_end]);
        }

        if ($request->filled('sore_start')) {
            Setting::where('key', 'sore_start')
                ->update(['value' => $request->sore_start]);
        }

        if ($request->filled('sore_end')) {
            Setting::where('key', 'sore_end')
                ->update(['value' => $request->sore_end]);
        }

        if ($request->filled('nama_sistem')) {
            Setting::where('key', 'name')
                ->update(['value' => $request->nama_sistem]);
        }

        // 7) Logo Sistem (bila ada upload)
        if ($request->hasFile('logo_sistem')) {
            $path = $request->file('logo_sistem')->store('logos', 'public');

            // Update path di tabel settings
            Setting::where('key', 'logo')
                ->update(['value' => 'storage/' . $path]);
        }

        // Berikan response JSON agar SweetAlert2 dapat menampilkannya
        return response()->json([
            'success' => 'Settings updated successfully!'
        ]);
    }
}
