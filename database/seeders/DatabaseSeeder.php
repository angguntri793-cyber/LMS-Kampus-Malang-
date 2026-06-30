<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AppNotification;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
        ]);

        $dosen2 = User::create([
            'nomor_induk' => 'D002',
            'name' => 'Faisal Rahman, M.T.',
            'email' => 'faisal@kampusmalang.ac.id',
            'role' => 'dosen',
            'password' => Hash::make('password'),
            'locale' => 'id',
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
        ]);

        $mhs2 = User::create([
            'nomor_induk' => '241111',
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@student.kampusmalang.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
            'locale' => 'id',
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
            ],
            [
                'nama_matkul' => 'Pemrograman Web',
                'kode' => 'IF302',
                'deskripsi' => 'Belajar pengembangan web modern menggunakan HTML, CSS, JavaScript, PHP, dan framework Laravel.',
                'dosen_id' => $dosen->id,
            ],
            [
                'nama_matkul' => 'Struktur Data',
                'kode' => 'IF201',
                'deskripsi' => 'Mempelajari organisasi data pada komputer seperti list, stack, queue, tree, dan graph.',
                'dosen_id' => $dosen2->id,
            ],
            [
                'nama_matkul' => 'Jaringan Komputer',
                'kode' => 'IF301',
                'deskripsi' => 'Pengenalan jaringan komputer, layer TCP/IP, routing, switching, dan keamanan jaringan dasar.',
                'dosen_id' => $dosen2->id,
            ],
            [
                'nama_matkul' => 'Pemrograman Berorientasi Objek',
                'kode' => 'IF202',
                'deskripsi' => 'Konsep OOP seperti inheritance, polymorphism, encapsulation, dan abstraction menggunakan Java.',
                'dosen_id' => $dosen2->id,
            ],
            [
                'nama_matkul' => 'Basis Data',
                'kode' => 'IF203',
                'deskripsi' => 'Desain ERD, normalisasi, dan implementasi kueri SQL menggunakan RDBMS.',
                'dosen_id' => $dosen->id,
            ],
            [
                'nama_matkul' => 'Rekayasa Perangkat Lunak',
                'kode' => 'IF304',
                'deskripsi' => 'Metodologi pengembangan perangkat lunak (SDLC), analisis kebutuhan, pengujian, dan pemeliharaan.',
                'dosen_id' => $dosen2->id,
            ]
        ];

        $courses = [];
        foreach ($coursesData as $cData) {
            $course = Course::create($cData);
            // Enroll students into all courses
            $course->students()->attach([$mhs1->id, $mhs2->id]);
            $courses[$cData['kode']] = $course;
        }

        // ==========================
        // ASSIGNMENTS
        // ==========================
        $a1 = Assignment::create([
            'course_id' => $courses['IF401']->id,
            'judul' => 'Milestone3',
            'deskripsi' => 'Implementasi algoritma pencarian heuristic A* pada kasus grid pathfinding.',
            'deadline' => now()->addDays(3),
            'allowed_types' => 'pdf,zip,jpg,png',
            'max_size_mb' => 20,
        ]);

        $a2 = Assignment::create([
            'course_id' => $courses['IF302']->id,
            'judul' => 'Tugas CRUD Laravel',
            'deskripsi' => 'Buat aplikasi CRUD sederhana dengan tema perpustakaan menggunakan Laravel.',
            'deadline' => now()->addDays(5),
            'allowed_types' => 'zip,rar',
            'max_size_mb' => 50,
        ]);

        $a3 = Assignment::create([
            'course_id' => $courses['IF301']->id,
            'judul' => 'Pengenalan Perangkat Jaringan',
            'deskripsi' => 'mahasiswa mampu mengenali, membedakan, dan mengidentifikasi fungsi dari perangkat jaringan',
            'deadline' => now()->addDays(2),
            'allowed_types' => 'pdf,docx',
            'max_size_mb' => 10,
        ]);

        // ==========================
        // NOTIFIKASI LOGIN (Andi Pratama)
        // ==========================
        AppNotification::create([
            'user_id' => $mhs1->id,
            'title' => 'New sign in to your LMS Kampus Malang account',
            'content' => json_encode([
                'account' => $mhs1->nomor_induk . ' ' . $mhs1->email,
                'time' => 'Tuesday, 23 June 2026, 10:55 AM',
                'device' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
                'ip' => '122.2.22',
            ]),
            'is_read' => false,
        ]);

        // ==========================
        // NOTIFIKASI TUGAS (Andi Pratama)
        // ==========================
        AppNotification::create([
            'user_id' => $mhs1->id,
            'title' => 'You have upcoming activities due',
            'content' => json_encode([
                'nim' => $mhs1->nomor_induk,
                'activity' => 'Milestone3',
                'when' => 'Friday, 26 June 2026, 12:00 AM',
                'course' => 'Kecerdasan Buatan',
                'assignment_id' => $a1->id,
            ]),
            'is_read' => false,
        ]);

        // ==========================
        // MOCK CHAT MESSAGES
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