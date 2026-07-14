<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller {
    
    public function index(Request $request) {
        $user = Auth::user();
        
        if ($user->isDosen()) {
            $query = $user->taughtCourses()->withCount('assignments','students');
        } else {
            $query = $user->courses()->with('dosen')->withCount('assignments');
        }

        // Search & Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_matkul', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $courses = $query->get();
        
        $semesters = Semester::orderByDesc('id')->get();
        $kelas = Kelas::orderBy('nama')->get();

        return view('courses.index', compact('courses', 'semesters', 'kelas'));
    }

    public function show(Course $course) {
        $course->load(['assignments', 'dosen', 'students', 'notes', 'quizzes.attempts' => function($q) {
            $q->where('user_id', Auth::id());
        }]);
        
        if (Auth::user()->isMahasiswa()) {
            Auth::user()->logActivity("Melihat detail mata kuliah: {$course->nama_matkul}");
        }

        return view('courses.show', compact('course'));
    }

    // Dosen buat course
    public function create() {
        $this->authorizeDosen();
        $semesters = Semester::orderByDesc('id')->get();
        $kelas = Kelas::orderBy('nama')->get();
        $prodis = Prodi::orderBy('nama')->get();

        return view('courses.create', compact('semesters', 'kelas', 'prodis'));
    }

    public function store(Request $request) {
        $this->authorizeDosen();
        $data = $request->validate([
            'kode' => 'required|unique:courses',
            'nama_matkul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'prodi_id' => 'nullable|exists:prodis,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);
        
        $data['dosen_id'] = Auth::id();
        $course = Course::create($data);
        
        // Enroll students
        if ($course->kelas_id) {
            $studentIds = User::where('role', 'mahasiswa')
                ->where('kelas_id', $course->kelas_id)
                ->pluck('id');
            $course->students()->sync($studentIds);
        } else {
            $course->students()->sync(User::where('role','mahasiswa')->pluck('id'));
        }

        Auth::user()->logActivity("Membuat Mata Kuliah baru: {$course->nama_matkul}");

        return redirect()->route('courses.show', $course)->with('success', __('Mata kuliah berhasil dibuat.'));
    }

    private function authorizeDosen() {
        abort_unless(Auth::user()->isDosen(), 403);
    }
}