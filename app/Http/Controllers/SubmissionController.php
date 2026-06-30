<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    // Mahasiswa upload tugas
    public function store(Request $request, Assignment $assignment)
    {
        abort_unless(Auth::user()->isMahasiswa(), 403);

        $allowed = $assignment->allowedArray();
        $maxKb = ($assignment->max_size_mb ?: 20) * 1024;

        $request->validate([
            'file' => 'required|file|max:' . $maxKb . '|mimes:' . implode(',', $allowed),
            'catatan' => 'nullable|string',
        ], [
            'file.mimes' => __('Format file tidak diizinkan. Hanya: ') . $assignment->allowed_types,
            'file.max' => __('Ukuran file melebihi batas maksimal: ') . $assignment->max_size_mb . ' MB',
        ]);

        $existing = $assignment->submissionFor(Auth::id());
        if ($existing && $existing->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $file = $request->file('file');
        $path = $file->store('submissions/' . $assignment->id, 'public');

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => Auth::id()],
            [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'catatan'   => $request->catatan,
                'submitted_at' => now(),
                'is_completed' => false,
            ]
        );

        return back()->with('success', __('Tugas berhasil diunggah.'));
    }

    // Tandai selesai
    public function markComplete(Submission $submission)
    {
        abort_unless($submission->user_id === Auth::id(), 403);
        $submission->update(['is_completed' => true]);
        return back()->with('success', __('Tugas ditandai selesai.'));
    }

    public function unmarkComplete(Submission $submission)
    {
        abort_unless($submission->user_id === Auth::id(), 403);
        $submission->update(['is_completed' => false]);
        return back()->with('success', __('Tanda selesai dibatalkan.'));
    }

    // Download (dosen / pemilik)
    public function download(Submission $submission)
    {
        $u = Auth::user();
        $isOwner = $u->id === $submission->user_id;
        $isLecturer = $u->id === $submission->assignment->course->dosen_id;
        $isAdmin = $u->isAdmin();

        abort_unless($isOwner || $isLecturer || $isAdmin, 403);
        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
    }

    // Dosen beri nilai
    public function grade(Request $request, Submission $submission)
    {
        abort_unless(Auth::id() === $submission->assignment->course->dosen_id, 403);
        $data = $request->validate([
            'nilai' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);
        $submission->update($data);
        return back()->with('success', __('Nilai tersimpan.'));
    }
}