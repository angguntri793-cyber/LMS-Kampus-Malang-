<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model {
    protected $table = 'kalender_akademiks';
    protected $fillable = ['kegiatan', 'tanggal_mulai', 'tanggal_selesai'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
