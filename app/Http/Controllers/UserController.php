<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Tampilkan halaman manajemen user
    public function index()
    {
        return view('admin.users');
    }

    // Mengembalikan data user untuk DataTables
    public function getUsers(Request $request)
    {
        $users = User::all();

        return DataTables::of($users)
            ->addIndexColumn() // Menambahkan nomor urut (DT_RowIndex)
            ->addColumn('action', function ($row) {
                // Tombol edit menggunakan modal (data-user berisi JSON user)
                $editIcon = '<button data-user=\'' . json_encode($row) . '\' onclick="editUser(this)" class="action-icon btn-edit" title="Edit User"><i class="fa-solid fa-pen-to-square"></i></button>';
                // Tombol delete
                $deleteIcon = '<button onclick="deleteUser(' . $row->id . ')" class="action-icon btn-delete" title="Hapus User"><i class="fa-solid fa-trash"></i></button>';
                // Tombol switch account (misal untuk impersonasi)
                $switchIcon = '<a href="' . route('switchAccount', $row->id) . '" class="action-icon btn-switch" title="Switch Account"><i class="fa-solid fa-user"></i></a>';
                return '<div class="flex justify-center gap-2">' . $editIcon . $deleteIcon . $switchIcon . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Menyimpan user baru (Create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,reviewer,petugas-kebersihan,juru-bengkel'
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role']
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Mengembalikan data user untuk keperluan edit (bisa digunakan untuk AJAX)
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user]);
    }

    // Memperbarui data user (Update)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,reviewer,petugas-kebersihan,juru-bengkel'
        ];

        // Jika password diisi, lakukan validasi tambahan
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validated = $request->validate($rules);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Menghapus user (Delete)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    public function switchAccount(Request $request, $id)
    {
        $user = Auth::user();
        $role = Auth::user()->role;
        if ($user && $role == 'admin') {
            // Simpan ID Pengguna asli di session sebelum switch
            session(['original_user_id' => $user->id]);

            // Login sebagai user lain dengan id yang dimasukkan
            auth()->guard('web')->loginUsingId($id);

            // Redirect ke halaman dashboard atau halamanan lainnya
            return redirect()->route('dashboard');
        }

        // Jika bukan admin, tampilkan erro 403
        abort(403);
    }

    public function switchBack()
    {
        if (session()->has('original_user_id')) {
            // Ambil ID user asli dan login kembali sebagai admin
            $originalUserId = session('original_user_id');
            auth()->guard('web')->loginUsingId($originalUserId);

            // Hapus ID user asli dari session
            session()->forget('original_user_id');

            // Redirect kembali ke halaman dashboard atau halaman lainnya
            return redirect()->route('user');
        }

        //jika tidak ada ID asli di session, tampilkan error 403
        abort(403);
    }

    // Method updatePassword untuk mengganti password default
    public function updatePassword(Request $request)
    {
        // Validasi: pastikan field password dan konfirmasi sesuai
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Optional: Pastikan bahwa hanya user dengan password default yang dipaksa mengganti
        if (!Hash::check('123123123', $user->password)) {
            return response()->json([
                'info' => 'Password Anda sudah diganti.'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => 'Password berhasil diperbarui!'
        ]);
    }
}
