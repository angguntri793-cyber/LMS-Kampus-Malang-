<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseNoteController extends Controller
{
    public function store(Request $request, Course $course)
    {
        abort_unless(auth()->user()->isDosen() && $course->dosen_id === auth()->id(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file' => 'nullable|file|max:20480', // 20 MB max
        ]);

        $note = new CourseNote([
            'course_id' => $course->id,
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('notes/' . $course->id, 'public');
            $note->file_path = $path;
            $note->file_name = $file->getClientOriginalName();
        }

        $note->save();

        return back()->with('success', __('Catatan kuliah berhasil ditambahkan.'));
    }

    public function update(Request $request, CourseNote $note)
    {
        $course = $note->course;
        abort_unless(auth()->user()->isDosen() && $course->dosen_id === auth()->id(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file' => 'nullable|file|max:20480',
        ]);

        $note->judul = $request->judul;
        $note->isi = $request->isi;

        if ($request->hasFile('file')) {
            if ($note->file_path) {
                Storage::disk('public')->delete($note->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('notes/' . $course->id, 'public');
            $note->file_path = $path;
            $note->file_name = $file->getClientOriginalName();
        }

        $note->save();

        return back()->with('success', __('Catatan kuliah berhasil diperbarui.'));
    }

    public function destroy(CourseNote $note)
    {
        $course = $note->course;
        abort_unless(auth()->user()->isDosen() && $course->dosen_id === auth()->id(), 403);

        if ($note->file_path) {
            Storage::disk('public')->delete($note->file_path);
        }

        $note->delete();

        return back()->with('success', __('Catatan kuliah berhasil dihapus.'));
    }

    public function download(CourseNote $note)
    {
        // Enrolled student or the lecturer can download
        $user = auth()->user();
        $course = $note->course;
        
        $isEnrolled = $course->students()->where('users.id', $user->id)->exists();
        $isLecturer = $course->dosen_id === $user->id;
        $isAdmin = $user->isAdmin();

        abort_unless($isEnrolled || $isLecturer || $isAdmin, 403);
        abort_unless($note->file_path, 404);

        return Storage::disk('public')->download($note->file_path, $note->file_name);
    }
}
