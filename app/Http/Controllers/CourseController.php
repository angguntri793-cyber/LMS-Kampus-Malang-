<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller {
    public function index() {
        $user = Auth::user();
        $courses = $user->isDosen()
            ? $user->taughtCourses()->withCount('assignments','students')->get()
            : $user->courses()->with('dosen')->withCount('assignments')->get();
        return view('courses.index', compact('courses'));
    }

    public function show(Course $course) {
        $course->load('assignments','dosen','students');
        return view('courses.show', compact('course'));
    }

    // Dosen buat course
    public function create() {
        $this->authorizeDosen();
        return view('courses.create');
    }

    public function store(Request $request) {
        $this->authorizeDosen();
        $data = $request->validate([
            'kode' => 'required|unique:courses',
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'warna' => 'nullable',
        ]);
        $data['dosen_id'] = Auth::id();
        $course = Course::create($data);
        // Enroll semua mahasiswa otomatis (demo)
        $course->students()->sync(User::where('role','mahasiswa')->pluck('id'));
        return redirect()->route('courses.show', $course)->with('success', __('Mata kuliah berhasil dibuat.'));
    }

    private function authorizeDosen() {
        abort_unless(Auth::user()->isDosen(), 403);
    }
}