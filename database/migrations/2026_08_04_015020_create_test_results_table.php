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
Schema::create('test_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('test_run_id')->constrained('test_runs')->onDelete('cascade');
        $table->foreignId('test_case_id')->constrained('test_cases')->onDelete('cascade');
        $table->enum('status', ['Untested', 'Passed', 'Failed', 'Blocked'])->default('Untested');
        $table->foreignId('executed_by')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
