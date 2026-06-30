<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nomor_induk' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nomor_induk', $request->nomor_induk)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nomor_induk' => 'Nomor Induk atau Password salah.',
            ])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Login Berhasil',
            'content' => json_encode([
                'account' => $user->nomor_induk,
                'email' => $user->email,
                'time' => now()->format('d-m-Y H:i:s'),
                'device' => $request->userAgent(),
                'ip' => $request->ip(),
            ]),
            'is_read' => false,
        ]);

        // Semua user menuju dashboard yang sama
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['id', 'en'])) {
            session(['locale' => $locale]);

            if (Auth::check()) {
                Auth::user()->update([
                    'locale' => $locale,
                ]);
            }
        }

        return back();
    }
}