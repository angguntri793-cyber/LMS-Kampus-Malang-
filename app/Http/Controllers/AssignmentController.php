<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $assignment->load('course', 'submissions.user');
        $mySubmission = Auth::user()->isMahasiswa()
            ? $assignment->submissionFor(Auth::id()) : null;
        return view('assignments.show', compact('assignment', 'mySubmission'));
    }

    // ===== DOSEN: CRUD =====
    public function create(Course $course)
    {
        $this->dosen($course);
        return view('assignments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->dosen($course);
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'allowed_types' => 'required|string',
            'max_size_mb' => 'required|integer|min:1',
        ]);
        $data['course_id'] = $course->id;
        $assignment = Assignment::create($data);

        // Notifikasi ke semua mahasiswa terdaftar
        foreach ($course->students as $std) {
            AppNotification::create([
                'user_id' => $std->id,
                'title' => 'You have upcoming activities due',
                'content' => json_encode([
                    'nim' => $std->nomor_induk,
                    'activity' => $assignment->judul,
                    'when' => \Carbon\Carbon::parse($assignment->deadline)->format('l, d F Y, g:i A'),
                    'course' => $course->nama_matkul,
                    'assignment_id' => $assignment->id,
                ]),
                'is_read' => false,
            ]);
        }

        return redirect()->route('courses.show', $course)->with('success', __('Tugas berhasil dibuat.'));
    }

    public function edit(Assignment $assignment)
    {
        $this->dosen($assignment->course);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->dosen($assignment->course);
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'allowed_types' => 'required|string',
            'max_size_mb' => 'required|integer|min:1',
        ]);
        $assignment->update($data);
        return redirect()->route('assignments.show', $assignment)->with('success', __('Tugas berhasil diperbarui.'));
    }

    public function destroy(Assignment $assignment)
    {
        $this->dosen($assignment->course);
        $course = $assignment->course;
        $assignment->delete();
        return redirect()->route('courses.show', $course)->with('success', __('Tugas berhasil dihapus.'));
    }

    private function dosen(Course $course)
    {
        abort_unless(Auth::user()->isDosen() && $course->dosen_id === Auth::id(), 403);
    }
}