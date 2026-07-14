<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->timestamps();
        });

        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "Ganjil 2025/2026"
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('tahun_akademiks', function (Blueprint $table) {
            $table->id();
            $table->string('tahun'); // e.g., "2025/2026"
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "IF-4A"
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tahun_akademiks');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('prodis');
        Schema::dropIfExists('fakultas');
    }
};
