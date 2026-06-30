<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline');
            $table->string('allowed_types')->default('pdf,zip,jpg,txt'); // ketentuan file
            $table->integer('max_size_mb')->default(20);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('assignments');
    }
};