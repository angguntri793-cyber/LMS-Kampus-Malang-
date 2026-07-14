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
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nomor_induk' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nomor_induk', $request->nomor_induk)
                    ->orWhere('email', $request->nomor_induk)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nomor_induk' => 'Nomor Induk atau Password salah.',
            ])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'nomor_induk' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
            ])->withInput();
        }

        $remember = $request->has('remember');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Parse user agent
        $userAgent = $request->userAgent();
        [$device, $browser] = $this->parseAgent($userAgent);

        // Update User last login fields
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_device' => $device,
            'last_login_browser' => $browser,
        ]);

        // Insert to login_histories
        $user->loginHistories()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device' => $device,
            'browser' => $browser,
            'created_at' => now(),
        ]);

        // Create notification
        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Login Berhasil',
            'content' => json_encode([
                'account' => $user->nomor_induk,
                'email' => $user->email,
                'time' => now()->translatedFormat('l, d F Y, H:i'),
                'device' => $device . ' (' . $browser . ')',
                'ip' => $request->ip(),
            ]),
            'type' => 'signin',
            'url' => '/profile',
            'is_read' => false,
        ]);

        $user->logActivity('Melakukan login ke sistem');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->logActivity('Melakukan logout dari sistem');
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function logoutAll(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Manually delete other sessions of this user from sessions table
        \DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', session()->getId())->delete();
        
        // standard laravel logout other devices (if password provided)
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|current_password',
            ]);
            Auth::logoutOtherDevices($request->password);
        }

        $user->logActivity('Melakukan logout dari seluruh perangkat lain');

        return back()->with('success', __('Berhasil keluar dari semua perangkat lain.'));
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

    private function parseAgent($userAgent)
    {
        $browser = 'Unknown Browser';
        $device = 'Unknown Device';

        if (stripos($userAgent, 'Windows') !== false) {
            $device = 'Windows PC';
        } elseif (stripos($userAgent, 'Macintosh') !== false) {
            $device = 'Mac';
        } elseif (stripos($userAgent, 'iPhone') !== false) {
            $device = 'iPhone';
        } elseif (stripos($userAgent, 'iPad') !== false) {
            $device = 'iPad';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $device = 'Android Device';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $device = 'Linux PC';
        }

        if (stripos($userAgent, 'Edge') !== false || stripos($userAgent, 'Edg') !== false) {
            $browser = 'Microsoft Edge';
        } elseif (stripos($userAgent, 'Chrome') !== false) {
            $browser = 'Google Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $browser = 'Mozilla Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            $browser = 'Apple Safari';
        } elseif (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) {
            $browser = 'Opera';
        }

        return [$device, $browser];
    }
}