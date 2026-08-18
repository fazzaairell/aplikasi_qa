<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_test_case_test_suite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_test_case_id')->constrained('master_test_cases')->onDelete('cascade');
            $table->foreignId('test_suite_id')->constrained('test_suites')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['master_test_case_id', 'test_suite_id'], 'master_suite_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_test_case_test_suite');
    }
};
