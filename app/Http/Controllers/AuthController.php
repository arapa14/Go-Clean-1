<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Location;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('dashboard')
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Login Berhasil');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'error' => 'Email atau password salah!'
            ], 422);
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

        // Hitung jumalah laporan
        $totalReports = Report::count();
        $totalPendingReports = Report::where('status', 'pending')->count();

        // Hitung jumlah upload hari ini
        $amountReportToday = Report::whereDate('created_at', $today)->count();

        // Hitung jumlah pengguna berdasarkan role
        $juruBengkelCount = User::where('role', 'juru-bengkel')->count();
        $petugasKebersihanCount = User::where('role', 'petugas-kebersihan')->count();

        // Hitung jumlah komplain
        $totalComplaints = Complaint::count();
        $totalNewComplaints = Complaint::where('status', 'pending')->count();

        // Ambil semua user dengan role juru-bengkel atau petugas-kebersihan
        $usersToCheck = User::whereIn('role', ['juru-bengkel', 'petugas-kebersihan'])->get();
        $usersWithReportToday = [];
        $usersWithoutReportToday = [];

        // Ganti variabel $user dengan $checkUser di dalam loop
        foreach ($usersToCheck as $checkUser) {
            $hasReportToday = Report::where('user_id', $checkUser->id)
                ->whereDate('created_at', $today)
                ->exists();

            if ($hasReportToday) {
                $usersWithReportToday[] = $checkUser;
            } else {
                $usersWithoutReportToday[] = $checkUser;
            }
        }

        $countUsersWithReportToday = count($usersWithReportToday);
        $countUsersWithoutReportToday = count($usersWithoutReportToday);

        // Cek apakah user menggunakan password default (123123123)
        $shouldChangePassword = false;
        if (Hash::check('123123123', $user->password)) {
            $shouldChangePassword = true;
        }

        // Buat array data yang akan dikirim ke view
        $analitik = compact(
            'juruBengkelCount',
            'petugasKebersihanCount',
            'totalReports',
            'amountReportToday',
            'totalPendingReports',
            'totalComplaints',
            'totalNewComplaints',
            'countUsersWithReportToday',
            'countUsersWithoutReportToday',
            'shouldChangePassword'
        );



        // Data yang umum untuk semua role
        $data = compact('user', 'locations', 'reports', 'reportToday', 'amountReportToday', 'shouldChangePassword');

        switch ($user->role) {
            case 'admin':
                return view('admin.dashboard', $analitik);
            case 'reviewer':
                return view('reviewer.dashboard', $analitik);
            case 'petugas-kebersihan':
                return view('petugas.dashboard', $data);
            case 'juru-bengkel':
                return view('petugas.dashboard', $data);
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
