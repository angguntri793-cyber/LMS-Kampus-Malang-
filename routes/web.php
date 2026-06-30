<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, CourseController, AssignmentController,
    SubmissionController, ChatController, ProfileController, NotificationController,
    OnlineController, AdminUserController, CourseNoteController
};

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->name('logout');
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

    // Submissions
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class,'store'])->name('submissions.store');
    Route::post('/submissions/{submission}/complete', [SubmissionController::class,'markComplete'])->name('submissions.complete');
    Route::post('/submissions/{submission}/uncomplete', [SubmissionController::class,'unmarkComplete'])->name('submissions.uncomplete');
    Route::get('/submissions/{submission}/download', [SubmissionController::class,'download'])->name('submissions.download');
    Route::post('/submissions/{submission}/grade', [SubmissionController::class,'grade'])->name('submissions.grade');

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

    // Admin User CRUD
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    });
});