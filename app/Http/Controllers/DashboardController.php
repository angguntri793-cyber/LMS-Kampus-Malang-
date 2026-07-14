<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Models\Submission;
use App\Models\Pengumuman;
use App\Models\KalenderAkademik;
use App\Models\Berita;
use App\Models\Banner;
use App\Models\UserActivity;
use App\Models\Prodi;
use App\Models\Kelas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Fetch notifications for all roles
        $notifications = $user->appNotifications()->take(10)->get();

        // ==========================
        // ADMIN DASHBOARD STATS
        // ==========================
        if ($user->isAdmin()) {
            $stats = [
                'total_students' => User::where('role', 'mahasiswa')->count(),
                'total_dosens' => User::where('role', 'dosen')->count(),
                'total_courses' => Course::count(),
                'total_prodi' => Prodi::count(),
                'total_kelas' => Kelas::count(),
                'total_activities' => UserActivity::count(),
            ];

            $recentActivities = UserActivity::with('user')
                ->latest()
                ->take(10)
                ->get();

            $banners = Banner::where('is_active', true)->orderBy('order')->get();
            $announcements = Pengumuman::with('user')->latest()->take(5)->get();
            $calendarEvents = KalenderAkademik::orderBy('tanggal_mulai')->take(5)->get();
            $news = Berita::latest()->take(3)->get();

            return view('dashboard', [
                'user' => $user,
                'stats' => $stats,
                'recentActivities' => $recentActivities,
                'banners' => $banners,
                'announcements' => $announcements,
                'calendarEvents' => $calendarEvents,
                'news' => $news,
                'notifications' => $notifications,
            ]);
        }

        // ==========================
        // DOSEN & MAHASISWA STATS
        // ==========================
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $announcements = Pengumuman::whereIn('target_role', ['all', $user->role])
            ->latest()
            ->take(5)
            ->get();
        $calendarEvents = KalenderAkademik::orderBy('tanggal_mulai')->take(5)->get();
        $news = Berita::latest()->take(3)->get();

        if ($user->isDosen()) {
            $courses = $user->taughtCourses()
                ->withCount(['assignments', 'students'])
                ->get();

            $courseIds = $courses->pluck('id');

            $upcoming = Assignment::whereIn('course_id', $courseIds)
                ->where('deadline', '>=', now())
                ->orderBy('deadline')
                ->take(5)
                ->get();

            $stats = [
                'courses_count' => $courses->count(),
                'students_count' => $courses->sum('students_count'),
                'assignments_count' => $courses->sum('assignments_count'),
            ];

            return view('dashboard', [
                'user' => $user,
                'courses' => $courses,
                'upcoming' => $upcoming,
                'stats' => $stats,
                'banners' => $banners,
                'announcements' => $announcements,
                'calendarEvents' => $calendarEvents,
                'news' => $news,
                'notifications' => $notifications,
            ]);
        }

        // Mahasiswa Dashboard
        $courses = $user->courses()
            ->with('dosen')
            ->withCount('assignments')
            ->get();

        $courseIds = $courses->pluck('id');

        $upcoming = Assignment::whereIn('course_id', $courseIds)
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Calculate student academic stats
        $totalSubmissions = $user->submissions()->count();
        $completedSubmissions = $user->submissions()->where('is_completed', true)->count();
        $averageGrade = $user->submissions()->whereNotNull('nilai')->avg('nilai') ?: 0;

        $stats = [
            'courses_count' => $courses->count(),
            'assignments_count' => Assignment::whereIn('course_id', $courseIds)->count(),
            'completed_count' => $completedSubmissions,
            'average_grade' => round($averageGrade, 2),
        ];

        return view('dashboard', [
            'user' => $user,
            'courses' => $courses,
            'upcoming' => $upcoming,
            'stats' => $stats,
            'banners' => $banners,
            'announcements' => $announcements,
            'calendarEvents' => $calendarEvents,
            'news' => $news,
            'notifications' => $notifications,
        ]);
    }
}