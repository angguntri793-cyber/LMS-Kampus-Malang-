<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->boolean('is_completed')->default(false);
            $table->dateTime('submitted_at')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('catatan')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('submissions');
    }
};