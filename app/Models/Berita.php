<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Berita extends Model {
    protected $table = 'beritas';
    protected $fillable = ['judul', 'konten', 'gambar', 'user_id'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
