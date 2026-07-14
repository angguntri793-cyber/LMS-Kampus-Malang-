<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AppNotification;
use App\Models\Message;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Quiz;
use App\Models\Pengumuman;
use App\Models\KalenderAkademik;
use App\Models\Berita;
use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================
        // FAKULTAS & PRODI
        // ==========================
        $f1 = Fakultas::create(['nama' => 'Fakultas Ilmu Komputer', 'kode' => 'FIK']);
        $f2 = Fakultas::create(['nama' => 'Fakultas Ekonomi dan Bisnis', 'kode' => 'FEB']);

        $p1 = Prodi::create(['nama' => 'Teknik Informatika', 'kode' => 'TI', 'fakultas_id' => $f1->id]);
        $p2 = Prodi::create(['nama' => 'Sistem Informasi', 'kode' => 'SI', 'fakultas_id' => $f1->id]);
        $p3 = Prodi::create(['nama' => 'Manajemen', 'kode' => 'MNJ', 'fakultas_id' => $f2->id]);

        // ==========================
        // SEMESTER & TAHUN AKADEMIK
        // ==========================
        $sem1 = Semester::create(['nama' => 'Ganjil 2025/2026', 'is_active' => true]);
        $sem2 = Semester::create(['nama' => 'Genap 2025/2026', 'is_active' => false]);

        $ta = TahunAkademik::create(['tahun' => '2025/2026', 'is_active' => true]);

        // ==========================
        // KELAS
        // ==========================
        $k1 = Kelas::create(['nama' => 'TI-4A', 'prodi_id' => $p1->id]);
        $k2 = Kelas::create(['nama' => 'TI-4B', 'prodi_id' => $p1->id]);
        $k3 = Kelas::create(['nama' => 'SI-2A', 'prodi_id' => $p2->id]);

        // ==========================
        // ADMIN
        // ==========================
        $admin = User::create([
            'nomor_induk' => 'admin',
            'name' => 'LMS Administrator',
            'email' => 'admin@kampusmalang.ac.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'is_active' => true,
        ]);

        // ==========================
        // DOSEN
        // ==========================
        $dosen = User::create([
            'nomor_induk' => 'D001',
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@kampusmalang.ac.id',
            'role' => 'dosen',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'jurusan' => 'Teknik Informatika',
            'prodi_id' => $p1->id,
            'is_active' => true,
        ]);

        $dosen2 = User::create([
            'nomor_induk' => 'D002',
            'name' => 'Faisal Rahman, M.T.',
            'email' => 'faisal@kampusmalang.ac.id',
            'role' => 'dosen',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'jurusan' => 'Teknik Informatika',
            'prodi_id' => $p1->id,
            'is_active' => true,
        ]);

        // ==========================
        // MAHASISWA
        // ==========================
        $mhs1 = User::create([
            'nomor_induk' => '2512345678',
            'name' => 'Andi Pratama',
            'email' => 'andi@student.kampusmalang.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'jurusan' => 'Teknik Informatika',
            'prodi_id' => $p1->id,
            'kelas_id' => $k1->id,
            'bio' => 'Mahasiswa Informatika angkatan 2025.',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Lowokwaru No. 12, Malang',
            'is_active' => true,
        ]);

        $mhs2 = User::create([
            'nomor_induk' => '241111',
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@student.kampusmalang.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'jurusan' => 'Teknik Informatika',
            'prodi_id' => $p1->id,
            'kelas_id' => $k1->id,
            'bio' => 'Mahasiswa Informatika angkatan 2024.',
            'telepon' => '08111122233',
            'alamat' => 'Jl. Sukarno Hatta No. 45, Malang',
            'is_active' => true,
        ]);

        // ==========================
        // BANNERS
        // ==========================
        Banner::create([
            'judul' => 'Selamat Datang di LMS Kampus Malang',
            'gambar' => 'banners/welcome.jpg',
            'link' => '#',
            'order' => 1,
            'is_active' => true,
        ]);
        Banner::create([
            'judul' => 'Ujian Akhir Semester Ganjil 2025/2026',
            'gambar' => 'banners/uas.jpg',
            'link' => '#',
            'order' => 2,
            'is_active' => true,
        ]);

        // ==========================
        // PENGUMUMAN
        // ==========================
        Pengumuman::create([
            'judul' => 'Pengisian KRS Ganjil 2025/2026',
            'konten' => 'Diberitahukan kepada seluruh mahasiswa Universitas Kampus Malang bahwa pengisian Kartu Rencana Studi (KRS) Semester Ganjil 2025/2026 akan dibuka mulai tanggal 10 Agustus 2026 hingga 24 Agustus 2026.',
            'user_id' => $admin->id,
            'target_role' => 'all',
        ]);
        Pengumuman::create([
            'judul' => 'Batas Akhir Input Nilai',
            'konten' => 'Diharapkan kepada seluruh Dosen untuk menyelesaikan input nilai tugas, kuis, UTS, dan UAS sebelum tanggal 15 Januari 2026.',
            'user_id' => $admin->id,
            'target_role' => 'dosen',
        ]);

        // ==========================
        // BERITA
        // ==========================
        Berita::create([
            'judul' => 'Tim Robotika Kampus Malang Raih Juara 1 Nasional',
            'konten' => 'Tim Robotika Universitas Kampus Malang berhasil meraih gelar juara pertama dalam Kontes Robot Indonesia (KRI) 2026 kategori robot berkaki. Kompetisi ketat ini diikuti oleh puluhan universitas ternama se-Indonesia.',
            'gambar' => 'news/robotika.jpg',
            'user_id' => $admin->id,
        ]);

        // ==========================
        // KALENDER AKADEMIK
        // ==========================
        KalenderAkademik::create([
            'kegiatan' => 'Mulai Perkuliahan Semester Ganjil',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2026-09-01',
        ]);
        KalenderAkademik::create([
            'kegiatan' => 'Ujian Tengah Semester (UTS)',
            'tanggal_mulai' => '2026-10-20',
            'tanggal_selesai' => '2026-10-30',
        ]);
        KalenderAkademik::create([
            'kegiatan' => 'Ujian Akhir Semester (UAS)',
            'tanggal_mulai' => '2026-12-15',
            'tanggal_selesai' => '2026-12-28',
        ]);

        // ==========================
        // COURSES (Informatics Prodi)
        // ==========================
        $coursesData = [
            [
                'nama_matkul' => 'Kecerdasan Buatan',
                'kode' => 'IF401',
                'deskripsi' => 'Mata kuliah Kecerdasan Buatan membahas logika fuzzy, jaringan saraf tiruan, dan machine learning.',
                'dosen_id' => $dosen->id,
                'prodi_id' => $p1->id,
                'semester_id' => $sem1->id,
                'kelas_id' => $k1->id,
            ],
            [
                'nama_matkul' => 'Pemrograman Web',
                'kode' => 'IF302',
                'deskripsi' => 'Belajar pengembangan web modern menggunakan HTML, CSS, JavaScript, PHP, dan framework Laravel.',
                'dosen_id' => $dosen->id,
                'prodi_id' => $p1->id,
                'semester_id' => $sem1->id,
                'kelas_id' => $k1->id,
            ],
            [
                'nama_matkul' => 'Jaringan Komputer',
                'kode' => 'IF301',
                'deskripsi' => 'Pengenalan jaringan komputer, layer TCP/IP, routing, switching, dan keamanan jaringan dasar.',
                'dosen_id' => $dosen2->id,
                'prodi_id' => $p1->id,
                'semester_id' => $sem1->id,
                'kelas_id' => $k1->id,
            ],
        ];

        $courses = [];
        foreach ($coursesData as $cData) {
            $course = Course::create($cData);
            // Enroll students
            $course->students()->attach([$mhs1->id, $mhs2->id]);
            $courses[$cData['kode']] = $course;
        }

        // ==========================
        // ASSIGNMENTS
        // ==========================
        $a1 = Assignment::create([
            'course_id' => $courses['IF401']->id,
            'judul' => 'Milestone 3',
            'deskripsi' => 'Implementasi algoritma pencarian heuristic A* pada kasus grid pathfinding.',
            'deadline' => now()->addDays(3),
            'allowed_types' => 'pdf,zip,jpg,png',
            'max_size_mb' => 20,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(3),
            'upload_limit' => 1,
            'max_score' => 100,
            'rubric' => '1. Ketepatan Algoritma (50 pts), 2. Desain Kode & Struktur (30 pts), 3. Dokumentasi PDF (20 pts)',
        ]);

        $a2 = Assignment::create([
            'course_id' => $courses['IF302']->id,
            'judul' => 'Tugas CRUD Laravel',
            'deskripsi' => 'Buat aplikasi CRUD sederhana dengan tema perpustakaan menggunakan Laravel.',
            'deadline' => now()->addDays(5),
            'allowed_types' => 'zip,rar',
            'max_size_mb' => 50,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'upload_limit' => 1,
            'max_score' => 100,
            'rubric' => '1. Fitur CRUD Lengkap (40 pts), 2. Relasi Database (30 pts), 3. Validasi Form (30 pts)',
        ]);

        // ==========================
        // QUIZZES
        // ==========================
        Quiz::create([
            'course_id' => $courses['IF401']->id,
            'judul' => 'Kuis 1 Pengenalan Kecerdasan Buatan',
            'deskripsi' => 'Kuis pilihan ganda singkat mengenai konsep agen cerdas, Turing test, dan sejarah AI.',
            'durasi_menit' => 15,
            'deadline' => now()->addDays(4),
        ]);

        // ==========================
        // APP NOTIFICATIONS (Andi Pratama)
        // ==========================
        AppNotification::create([
            'user_id' => $mhs1->id,
            'title' => 'Percobaan Login Berhasil',
            'content' => json_encode([
                'account' => $mhs1->nomor_induk . ' ' . $mhs1->email,
                'time' => 'Tuesday, 23 June 2026, 10:55 AM',
                'device' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0',
                'ip' => '122.2.22',
            ]),
            'type' => 'signin',
            'url' => '/profile',
            'is_read' => false,
        ]);

        AppNotification::create([
            'user_id' => $mhs1->id,
            'title' => 'Tugas Mendatang Perlu Dikerjakan',
            'content' => json_encode([
                'nim' => $mhs1->nomor_induk,
                'activity' => 'Milestone 3',
                'when' => now()->addDays(3)->format('l, d F Y, g:i A'),
                'course' => 'Kecerdasan Buatan',
                'assignment_id' => $a1->id,
            ]),
            'type' => 'activity_due',
            'url' => '/assignments/' . $a1->id,
            'is_read' => false,
        ]);

        // ==========================
        // CHAT MESSAGES
        // ==========================
        Message::create([
            'sender_id' => $dosen->id,
            'receiver_id' => $mhs1->id,
            'body' => 'Halo Andi, bagaimana progres pengerjaan Milestone 3 Kecerdasan Buatan?',
            'is_read' => true,
        ]);

        Message::create([
            'sender_id' => $mhs1->id,
            'receiver_id' => $dosen->id,
            'body' => 'Halo Pak Budi, saya sedang mengimplementasikan fungsi heuristiknya. Apakah ada kendala jika saya kirim format .zip?',
            'is_read' => false,
        ]);
    }
}