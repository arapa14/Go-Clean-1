<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Report;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        $logo = Setting::where('key', 'logo')->first()->value;
        $name = Setting::where('key', 'name')->first()->value;
        return view('auth.login', compact(['logo', 'name']));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard')->with('success', 'Login Berhasil');
        }

        return redirect()->back()->with('error', 'Email atau password salah!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $locations = Location::all();
        $today = Carbon::today();

        $reports = Report::orderBy('created_at', 'desc')->paginate(10);

        // Ambil laporan user hari ini
        $reportToday = Report::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->take(3)
            ->get();

        // Hitung jumlah upload hari ini
        $amountReportToday = $reportToday->count();

        // Data yang umum untuk semua role
        $data = compact('user', 'locations', 'reports', 'reportToday', 'amountReportToday');

        // Pilih dashboard berdasarkan peran pengguna
        switch ($user->role) {
            case 'admin':
                return view('admin.dashboard', $data);
            case 'reviewer':
                return view('reviewer.dashboard', $data);
            case 'petugas-kebersihan':
                return view('petugas-kebersihan.dashboard', $data);
            case 'juru-bengkel':
                return view('juru-bengkel.dashboard', $data);
            default:
                return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }
    }



    public function getServerTime()
    {
        return response()->json([
            'time' => Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i:s')
        ]);
    }
}
