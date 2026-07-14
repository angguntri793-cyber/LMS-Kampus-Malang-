<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, CourseController, AssignmentController,
    SubmissionController, ChatController, ProfileController, NotificationController,
    OnlineController, AdminUserController, CourseNoteController, AdminCrudController,
    QuizController
};

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->name('logout');
Route::post('/logout-all', [AuthController::class,'logoutAll'])->name('logout.all');
Route::get('/locale/{locale}', [AuthController::class,'setLocale'])->name('locale.set');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Courses
    Route::get('/courses', [CourseController::class,'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class,'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class,'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class,'show'])->name('courses.show');

    // Course Notes (Catatan Kuliah)
    Route::post('/courses/{course}/notes', [CourseNoteController::class,'store'])->name('notes.store');
    Route::put('/notes/{note}', [CourseNoteController::class,'update'])->name('notes.update');
    Route::delete('/notes/{note}', [CourseNoteController::class,'destroy'])->name('notes.destroy');
    Route::get('/notes/{note}/download', [CourseNoteController::class,'download'])->name('notes.download');

    // Assignments
    Route::get('/courses/{course}/assignments/create', [AssignmentController::class,'create'])->name('assignments.create');
    Route::post('/courses/{course}/assignments', [AssignmentController::class,'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [AssignmentController::class,'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class,'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AssignmentController::class,'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class,'destroy'])->name('assignments.destroy');
    Route::get('/assignments/{assignment}/download-zip', [AssignmentController::class, 'downloadZip'])->name('assignments.download-zip');

    // Submissions
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class,'store'])->name('submissions.store');
    Route::delete('/submissions/{submission}', [SubmissionController::class,'destroy'])->name('submissions.destroy');
    Route::post('/submissions/{submission}/complete', [SubmissionController::class,'markComplete'])->name('submissions.complete');
    Route::post('/submissions/{submission}/uncomplete', [SubmissionController::class,'unmarkComplete'])->name('submissions.uncomplete');
    Route::get('/submissions/{submission}/download', [SubmissionController::class,'download'])->name('submissions.download');
    Route::post('/submissions/{submission}/grade', [SubmissionController::class,'grade'])->name('submissions.grade');

    // Quizzes
    Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');

    // Chat
    Route::get('/chat/{user?}', [ChatController::class,'index'])->name('chat.index');
    Route::post('/chat/{user}/send', [ChatController::class,'send'])->name('chat.send');
    Route::get('/chat/{user}/fetch', [ChatController::class,'fetch'])->name('chat.fetch');
    Route::delete('/messages/{message}', [ChatController::class,'destroy'])->name('chat.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class,'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class,'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class,'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class,'show'])->name('notifications.show');
    Route::post('/notifications/read-all', [NotificationController::class,'readAll'])->name('notifications.readAll');

    // Online users
    Route::get('/online', [OnlineController::class,'index'])->name('online.index');
    Route::get('/online/api', [OnlineController::class,'fetchApi'])->name('online.api');

    // Admin User CRUD & Structures
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/admin/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('admin.users.toggle');
        Route::post('/admin/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
        
        // Import & Export Excel (CSV)
        Route::get('/admin/users/export/csv', [AdminUserController::class, 'exportCsv'])->name('admin.users.export.csv');
        Route::get('/admin/users/export/pdf', [AdminUserController::class, 'exportPdf'])->name('admin.users.export.pdf');
        Route::post('/admin/users/import/csv', [AdminUserController::class, 'importCsv'])->name('admin.users.import.csv');

        // CRUD Fakultas
        Route::get('/admin/fakultas', [AdminCrudController::class, 'fakultasIndex'])->name('admin.fakultas.index');
        Route::post('/admin/fakultas', [AdminCrudController::class, 'fakultasStore'])->name('admin.fakultas.store');
        Route::put('/admin/fakultas/{fakultas}', [AdminCrudController::class, 'fakultasUpdate'])->name('admin.fakultas.update');
        Route::delete('/admin/fakultas/{fakultas}', [AdminCrudController::class, 'fakultasDestroy'])->name('admin.fakultas.destroy');

        // CRUD Prodi
        Route::get('/admin/prodi', [AdminCrudController::class, 'prodiIndex'])->name('admin.prodi.index');
        Route::post('/admin/prodi', [AdminCrudController::class, 'prodiStore'])->name('admin.prodi.store');
        Route::put('/admin/prodi/{prodi}', [AdminCrudController::class, 'prodiUpdate'])->name('admin.prodi.update');
        Route::delete('/admin/prodi/{prodi}', [AdminCrudController::class, 'prodiDestroy'])->name('admin.prodi.destroy');

        // CRUD Semester
        Route::get('/admin/semesters', [AdminCrudController::class, 'semesterIndex'])->name('admin.semesters.index');
        Route::post('/admin/semesters', [AdminCrudController::class, 'semesterStore'])->name('admin.semesters.store');
        Route::put('/admin/semesters/{semester}', [AdminCrudController::class, 'semesterUpdate'])->name('admin.semesters.update');
        Route::delete('/admin/semesters/{semester}', [AdminCrudController::class, 'semesterDestroy'])->name('admin.semesters.destroy');
        Route::post('/admin/semesters/{semester}/toggle', [AdminCrudController::class, 'semesterToggle'])->name('admin.semesters.toggle');

        // CRUD Tahun Akademik
        Route::get('/admin/tahun-akademik', [AdminCrudController::class, 'tahunAkademikIndex'])->name('admin.tahun-akademik.index');
        Route::post('/admin/tahun-akademik', [AdminCrudController::class, 'tahunAkademikStore'])->name('admin.tahun-akademik.store');
        Route::put('/admin/tahun-akademik/{tahun}', [AdminCrudController::class, 'tahunAkademikUpdate'])->name('admin.tahun-akademik.update');
        Route::delete('/admin/tahun-akademik/{tahun}', [AdminCrudController::class, 'tahunAkademikDestroy'])->name('admin.tahun-akademik.destroy');
        Route::post('/admin/tahun-akademik/{tahun}/toggle', [AdminCrudController::class, 'tahunAkademikToggle'])->name('admin.tahun-akademik.toggle');

        // CRUD Kelas
        Route::get('/admin/kelas', [AdminCrudController::class, 'kelasIndex'])->name('admin.kelas.index');
        Route::post('/admin/kelas', [AdminCrudController::class, 'kelasStore'])->name('admin.kelas.store');
        Route::put('/admin/kelas/{kelas}', [AdminCrudController::class, 'kelasUpdate'])->name('admin.kelas.update');
        Route::delete('/admin/kelas/{kelas}', [AdminCrudController::class, 'kelasDestroy'])->name('admin.kelas.destroy');

        // CRUD Pengumuman
        Route::get('/admin/pengumuman', [AdminCrudController::class, 'pengumumanIndex'])->name('admin.pengumuman.index');
        Route::post('/admin/pengumuman', [AdminCrudController::class, 'pengumumanStore'])->name('admin.pengumuman.store');
        Route::put('/admin/pengumuman/{pengumuman}', [AdminCrudController::class, 'pengumumanUpdate'])->name('admin.pengumuman.update');
        Route::delete('/admin/pengumuman/{pengumuman}', [AdminCrudController::class, 'pengumumanDestroy'])->name('admin.pengumuman.destroy');

        // CRUD Kalender Akademik
        Route::get('/admin/kalender', [AdminCrudController::class, 'kalenderIndex'])->name('admin.kalender.index');
        Route::post('/admin/kalender', [AdminCrudController::class, 'kalenderStore'])->name('admin.kalender.store');
        Route::put('/admin/kalender/{kalender}', [AdminCrudController::class, 'kalenderUpdate'])->name('admin.kalender.update');
        Route::delete('/admin/kalender/{kalender}', [AdminCrudController::class, 'kalenderDestroy'])->name('admin.kalender.destroy');

        // CRUD Berita
        Route::get('/admin/berita', [AdminCrudController::class, 'beritaIndex'])->name('admin.berita.index');
        Route::post('/admin/berita', [AdminCrudController::class, 'beritaStore'])->name('admin.berita.store');
        Route::put('/admin/berita/{berita}', [AdminCrudController::class, 'beritaUpdate'])->name('admin.berita.update');
        Route::delete('/admin/berita/{berita}', [AdminCrudController::class, 'beritaDestroy'])->name('admin.berita.destroy');

        // CRUD Banner
        Route::get('/admin/banners', [AdminCrudController::class, 'bannerIndex'])->name('admin.banners.index');
        Route::post('/admin/banners', [AdminCrudController::class, 'bannerStore'])->name('admin.banners.store');
        Route::put('/admin/banners/{banner}', [AdminCrudController::class, 'bannerUpdate'])->name('admin.banners.update');
        Route::delete('/admin/banners/{banner}', [AdminCrudController::class, 'bannerDestroy'])->name('admin.banners.destroy');
        Route::post('/admin/banners/{banner}/toggle', [AdminCrudController::class, 'bannerToggle'])->name('admin.banners.toggle');

        // CRUD Mata Kuliah (Courses) for Admin
        Route::get('/admin/courses', [AdminCrudController::class, 'courseIndex'])->name('admin.courses.index');
        Route::post('/admin/courses', [AdminCrudController::class, 'courseStore'])->name('admin.courses.store');
        Route::put('/admin/courses/{course}', [AdminCrudController::class, 'courseUpdate'])->name('admin.courses.update');
        Route::delete('/admin/courses/{course}', [AdminCrudController::class, 'courseDestroy'])->name('admin.courses.destroy');
        Route::get('/admin/courses/{course}/enrollments', [AdminCrudController::class, 'courseEnrollments'])->name('admin.courses.enrollments');
        Route::post('/admin/courses/{course}/enrollments', [AdminCrudController::class, 'courseEnrollmentsStore'])->name('admin.courses.enrollments.store');

        // View User Activities Logs
        Route::get('/admin/logs', [AdminCrudController::class, 'logsIndex'])->name('admin.logs.index');
    });
});