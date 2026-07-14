<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model {
    protected $table = 'prodis';
    protected $fillable = ['nama', 'kode', 'fakultas_id'];

    public function fakultas(): BelongsTo {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function users(): HasMany {
        return $this->hasMany(User::class, 'prodi_id');
    }

    public function courses(): HasMany {
        return $this->hasMany(Course::class, 'prodi_id');
    }
}
