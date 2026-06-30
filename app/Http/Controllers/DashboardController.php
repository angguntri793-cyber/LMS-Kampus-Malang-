<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin redirect to user management
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index');
        }

        if ($user->isDosen()) {
            $courses = $user->taughtCourses()
                ->withCount('assignments')
                ->withCount('students')
                ->get();

            $upcoming = Assignment::whereIn(
                    'course_id',
                    $user->taughtCourses()->pluck('id')
                )
                ->where('deadline', '>=', now())
                ->orderBy('deadline')
                ->take(5)
                ->get();
        } else {
            $courses = $user->courses()
                ->with('dosen')
                ->get();

            $upcoming = Assignment::whereIn(
                    'course_id',
                    $user->courses()->pluck('courses.id')
                )
                ->where('deadline', '>=', now())
                ->orderBy('deadline')
                ->take(5)
                ->get();
        }

        $notifications = $user->appNotifications()
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'courses' => $courses,
            'upcoming' => $upcoming,
            'notifications' => $notifications,
        ]);
    }
}