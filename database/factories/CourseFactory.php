<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'nama_matkul' => fake()->randomElement([
                'Pemrograman Web',
                'Basis Data',
                'Kecerdasan Buatan',
                'Algoritma dan Struktur Data',
                'Jaringan Komputer',
            ]),
            'kode' => fake()->unique()->bothify('IF###'),
            'deskripsi' => fake()->sentence(10),
            'dosen_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}