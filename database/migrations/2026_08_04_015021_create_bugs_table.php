<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_result_id')->constrained('test_results')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['Done in Review', 'in Progress'])->default('in Progress');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->string('attachment')->nullable(); // Kolom untuk menyimpan path file/foto bukti dokumentasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bugs');
    }
};