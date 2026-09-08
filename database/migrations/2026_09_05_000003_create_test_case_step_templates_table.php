<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_case_step_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_template_id')
                  ->constrained('test_case_templates')
                  ->onDelete('cascade');
            $table->unsignedInteger('step_number');
            $table->text('description');
            $table->text('expected_result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_case_step_templates');
    }
};
