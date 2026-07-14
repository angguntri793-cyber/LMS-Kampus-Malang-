<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model {
    protected $table = 'kelas';
    protected $fillable = ['nama', 'prodi_id'];

    public function prodi(): BelongsTo {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function users(): HasMany {
        return $this->hasMany(User::class, 'kelas_id');
    }

    public function courses(): HasMany {
        return $this->hasMany(Course::class, 'kelas_id');
    }
}
