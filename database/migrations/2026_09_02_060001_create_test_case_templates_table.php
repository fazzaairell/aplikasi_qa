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
        Schema::create('test_case_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_suite_template_id')
                  ->constrained('test_suite_templates')
                  ->onDelete('cascade');
            $table->string('title');
            $table->text('steps')->nullable();
            $table->text('expected_result')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_case_templates');
    }
};
