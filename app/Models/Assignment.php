<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'deskripsi',
        'deadline',
        'allowed_types',
        'max_size_mb',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function submissionFor($userId)
    {
        return $this->submissions()
            ->where('user_id', $userId)
            ->first();
    }

    public function allowedArray(): array
    {
        return array_map(
            'trim',
            explode(',', $this->allowed_types)
        );
    }
}