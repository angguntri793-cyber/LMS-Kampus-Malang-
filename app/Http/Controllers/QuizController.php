<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Course;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function create(Course $course)
    {
        abort_unless(Auth::user()->isDosen() && $course->dosen_id === Auth::id(), 403);
        return view('quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        abort_unless(Auth::user()->isDosen() && $course->dosen_id === Auth::id(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'deadline' => 'required|date|after:now',
            'questions_json' => 'required|string',
        ]);

        // Validate JSON
        $questions = json_decode($request->questions_json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($questions)) {
            return back()->withErrors(['questions_json' => 'Format JSON pertanyaan tidak valid. Pastikan sesuai contoh template.'])->withInput();
        }

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'judul' => $request->judul,
            'deskripsi' => $request->questions_json, // Store questions directly here
            'durasi_menit' => $request->durasi_menit,
            'deadline' => $request->deadline,
        ]);

        // Send notifications to all students
        foreach ($course->students as $std) {
            AppNotification::create([
                'user_id' => $std->id,
                'title' => 'Kuis Baru Tersedia',
                'content' => json_encode([
                    'quiz_id' => $quiz->id,
                    'course' => $course->nama_matkul,
                    'title' => $quiz->judul,
                    'deadline' => $quiz->deadline->translatedFormat('d F Y, H:i'),
                ]),
                'type' => 'new_quiz',
                'url' => '/quizzes/' . $quiz->id,
            ]);
        }

        Auth::user()->logActivity("Membuat Kuis baru: {$quiz->judul} di {$course->nama_matkul}");

        return redirect()->route('courses.show', $course)->with('success', __('Kuis berhasil dibuat.'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('course');
        $user = Auth::user();

        if ($user->isMahasiswa()) {
            // Check if student already attempted
            $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->first();

            $questions = json_decode($quiz->deskripsi, true) ?: [];

            return view('quizzes.show', compact('quiz', 'attempt', 'questions'));
        }

        // Dosen & Admin: view attempts list
        $attempts = QuizAttempt::with('user')
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('score')
            ->get();

        return view('quizzes.attempts', compact('quiz', 'attempts'));
    }

    public function edit(Quiz $quiz)
    {
        abort_unless(Auth::user()->isDosen() && $quiz->course->dosen_id === Auth::id(), 403);
        return view('quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        abort_unless(Auth::user()->isDosen() && $quiz->course->dosen_id === Auth::id(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'questions_json' => 'required|string',
        ]);

        $questions = json_decode($request->questions_json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($questions)) {
            return back()->withErrors(['questions_json' => 'Format JSON pertanyaan tidak valid.'])->withInput();
        }

        $quiz->update([
            'judul' => $request->judul,
            'durasi_menit' => $request->durasi_menit,
            'deadline' => $request->deadline,
            'deskripsi' => $request->questions_json,
        ]);

        Auth::user()->logActivity("Memperbarui Kuis ID {$quiz->id}: {$quiz->judul}");

        return redirect()->route('quizzes.show', $quiz)->with('success', __('Kuis berhasil diperbarui.'));
    }

    public function destroy(Quiz $quiz)
    {
        abort_unless(Auth::user()->isDosen() && $quiz->course->dosen_id === Auth::id(), 403);
        $course = $quiz->course;
        
        Auth::user()->logActivity("Menghapus Kuis: {$quiz->judul}");
        $quiz->delete();

        return redirect()->route('courses.show', $course)->with('success', __('Kuis berhasil dihapus.'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        abort_unless(Auth::user()->isMahasiswa(), 403);

        // Check if already submitted
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'Anda sudah mengerjakan kuis ini.']);
        }

        $questions = json_decode($quiz->deskripsi, true) ?: [];
        $totalQuestions = count($questions);
        $correctAnswers = 0;
        $answers = $request->input('answers', []);

        foreach ($questions as $index => $q) {
            $submittedAns = $answers[$index] ?? null;
            if ($submittedAns !== null && strtoupper($submittedAns) === strtoupper($q['answer'])) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'score' => $score,
            'answers_json' => json_encode($answers),
            'submitted_at' => now(),
        ]);

        // Send notification
        AppNotification::create([
            'user_id' => Auth::id(),
            'title' => 'Kuis Telah Dikumpulkan',
            'content' => json_encode([
                'quiz' => $quiz->judul,
                'score' => $score,
                'time' => now()->translatedFormat('d F Y, H:i'),
            ]),
            'type' => 'quiz_graded',
            'url' => '/quizzes/' . $quiz->id,
        ]);

        Auth::user()->logActivity("Mengerjakan Kuis: {$quiz->judul} dengan skor {$score}");

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Kuis berhasil diselesaikan dengan skor ' . $score . '!');
    }
}
