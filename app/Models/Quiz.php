<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model {
    protected $table = 'quizzes';
    protected $fillable = ['course_id', 'judul', 'deskripsi', 'durasi_menit', 'deadline'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function course(): BelongsTo {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function attempts(): HasMany {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}
