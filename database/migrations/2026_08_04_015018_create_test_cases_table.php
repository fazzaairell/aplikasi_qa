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
Schema::create('test_cases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('test_suite_id')->constrained('test_suites')->onDelete('cascade');
        $table->foreignId('requirement_id')->constrained('requirements')->onDelete('cascade');
        $table->string('title');
        $table->text('steps');
        $table->text('expected_result');
        $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};
