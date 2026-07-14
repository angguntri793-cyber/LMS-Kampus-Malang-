<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model {
    protected $table = 'semesters';
    protected $fillable = ['nama', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function courses(): HasMany {
        return $this->hasMany(Course::class, 'semester_id');
    }
}
