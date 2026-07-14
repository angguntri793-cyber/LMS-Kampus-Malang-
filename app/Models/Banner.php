<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $table = 'banners';
    protected $fillable = ['judul', 'gambar', 'link', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
