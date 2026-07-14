<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model {
    protected $table = 'quiz_attempts';
    protected $fillable = ['quiz_id', 'user_id', 'score', 'answers_json', 'submitted_at'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function quiz(): BelongsTo {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
