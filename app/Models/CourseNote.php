<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'isi',
        'file_path',
        'file_name',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
