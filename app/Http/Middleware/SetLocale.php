<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale {
    public function handle($request, Closure $next) {
        $locale = session('locale');
        if (!$locale && Auth::check()) {
            $locale = Auth::user()->locale;
        }
        $locale = $locale ?: 'id';
        App::setLocale($locale);
        session(['locale' => $locale]);

        if (Auth::check()) {
            // Update online status (max tiap 1 menit)
            $user = Auth::user();
            if (!$user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
                $user->forceFill(['last_seen_at' => now()])->save();
            }
        }
        return $next($request);
    }
}