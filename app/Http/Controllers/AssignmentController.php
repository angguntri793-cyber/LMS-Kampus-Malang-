<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $assignment->load('course', 'submissions.user');
        $mySubmission = Auth::user()->isMahasiswa()
            ? $assignment->submissionFor(Auth::id()) : null;
            
        if (Auth::user()->isMahasiswa()) {
            Auth::user()->logActivity("Melihat detail tugas: {$assignment->judul}");
        }
            
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'upload_limit' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
            'rubric' => 'nullable|string',
        ]);
        
        $data['course_id'] = $course->id;
        $assignment = Assignment::create($data);

        // Notifikasi ke semua mahasiswa terdaftar
        foreach ($course->students as $std) {
            AppNotification::create([
                'user_id' => $std->id,
                'title' => 'Tugas Baru Tersedia',
                'content' => json_encode([
                    'nim' => $std->nomor_induk,
                    'activity' => $assignment->judul,
                    'when' => \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l, d F Y, H:i'),
                    'course' => $course->nama_matkul,
                    'assignment_id' => $assignment->id,
                ]),
                'type' => 'new_assignment',
                'url' => '/assignments/' . $assignment->id,
                'is_read' => false,
            ]);
        }

        Auth::user()->logActivity("Membuat Tugas baru: {$assignment->judul} di {$course->nama_matkul}");

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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'upload_limit' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
            'rubric' => 'nullable|string',
        ]);
        
        $assignment->update($data);
        Auth::user()->logActivity("Memperbarui Tugas ID {$assignment->id}: {$assignment->judul}");

        return redirect()->route('assignments.show', $assignment)->with('success', __('Tugas berhasil diperbarui.'));
    }

    public function destroy(Assignment $assignment)
    {
        $this->dosen($assignment->course);
        $course = $assignment->course;
        
        Auth::user()->logActivity("Menghapus Tugas: {$assignment->judul}");
        $assignment->delete();

        return redirect()->route('courses.show', $course)->with('success', __('Tugas berhasil dihapus.'));
    }

    public function downloadZip(Assignment $assignment)
    {
        $this->dosen($assignment->course);
        
        $submissions = $assignment->submissions()->with('user')->get();
        if ($submissions->isEmpty()) {
            return back()->withErrors([__('Belum ada mahasiswa yang mengumpulkan tugas ini.')]);
        }

        $zip = new \ZipArchive();
        $fileName = 'submissions_' . str_replace(' ', '_', $assignment->judul) . '_' . date('Ymd_His') . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($submissions as $sub) {
                if ($sub->file_path && Storage::disk('public')->exists($sub->file_path)) {
                    $fileContent = Storage::disk('public')->get($sub->file_path);
                    $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $sub->user->name);
                    $zipEntryName = $sub->user->nomor_induk . '_' . $safeName . '_' . $sub->file_name;
                    $zip->addFromString($zipEntryName, $fileContent);
                }
            }
            $zip->close();
        } else {
            return back()->withErrors([__('Gagal membuat file ZIP.')]);
        }

        Auth::user()->logActivity("Mengunduh semua file tugas ZIP untuk kuis: {$assignment->judul}");

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function dosen(Course $course)
    {
        abort_unless(Auth::user()->isDosen() && $course->dosen_id === Auth::id(), 403);
    }
}