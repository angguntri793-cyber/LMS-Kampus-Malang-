<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model {
    protected $table = 'tahun_akademiks';
    protected $fillable = ['tahun', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
