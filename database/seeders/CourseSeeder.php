<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create([
            'nama_matkul' => 'Kecerdasan Buatan',
            'kode' => 'IF401',
            'deskripsi' => 'Mata kuliah AI dasar.',
            'dosen_id' => 1,
        ]);

        Course::create([
            'nama_matkul' => 'Pemrograman Web',
            'kode' => 'IF402',
            'deskripsi' => 'Belajar Laravel dan PHP.',
            'dosen_id' => 1,
        ]);

        Course::create([
            'nama_matkul' => 'Basis Data',
            'kode' => 'IF403',
            'deskripsi' => 'Belajar MySQL dan SQL.',
            'dosen_id' => 1,
        ]);
    }
}