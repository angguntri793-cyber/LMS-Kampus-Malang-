<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model {
    protected $table = 'pengumumans';
    protected $fillable = ['judul', 'konten', 'user_id', 'target_role'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
