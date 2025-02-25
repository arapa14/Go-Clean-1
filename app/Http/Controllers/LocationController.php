<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    // Tampilkan halaman manajemen location
    public function index()
    {
        return view('admin.locations');
    }

    // Mengembalikan data location untuk DataTables
    public function getLocations(Request $request)
    {
        $locations = Location::all();

        return DataTables::of($locations)
            ->addIndexColumn() // Menambahkan nomor urut (DT_RowIndex)
            ->addColumn('action', function ($row) {
                // Tombol edit (mengirim data location dalam format JSON)
                $editIcon = '<button data-location=\'' . json_encode($row) . '\' onclick="editLocation(this)" class="action-icon btn-edit" title="Edit Location"><i class="fa-solid fa-pen-to-square"></i></button>';
                // Tombol delete
                $deleteIcon = '<button onclick="deleteLocation(' . $row->id . ')" class="action-icon btn-delete" title="Delete Location"><i class="fa-solid fa-trash"></i></button>';
                return '<div class="flex justify-center gap-2">' . $editIcon . $deleteIcon . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Simpan data location baru (Create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
        ]);

        $location = Location::create($validated);

        return response()->json(['success' => true, 'location' => $location]);
    }

    // Mengembalikan data location untuk keperluan edit (misalnya via AJAX)
    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return response()->json(['location' => $location]);
    }

    // Perbarui data location (Update)
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'location' => 'required|string|max:255',
        ]);

        $location->update($validated);

        return response()->json(['success' => true, 'location' => $location]);
    }

    // Hapus data location (Delete)
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['success' => true]);
    }
}
