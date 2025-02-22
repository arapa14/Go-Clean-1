<?php

namespace App\Http\Controllers;

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
        switch ($user->role) {
            case 'admin':
                return view('admin.dashboard');
            case 'reviewer':
                return view('reviewer.dashboard');
            case 'petugas-kebersihan':
                return view('petugas-kebersihan.dashboard');
            case 'juru-bengkel':
                return view('juru-bengkel.dashboard');
            default:
                return redirect()->back()->with('error', 'Unauthenticated');
        }
    }


    public function getServerTime()
    {
        return response()->json([
            'time' => Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i:s')
        ]);
    }
}
