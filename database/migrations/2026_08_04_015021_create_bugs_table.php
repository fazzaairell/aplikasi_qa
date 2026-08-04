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
        $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed', 'Reopened'])->default('Open');
        $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
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
