<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_retests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bug_id')
                ->constrained('bugs')
                ->cascadeOnDelete();

            $table->foreignId('bug_fix_id')
                ->constrained('bug_fixes')
                ->cascadeOnDelete();

            $table->foreignId('qa_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('result', [
                'Passed',
                'Failed',
            ]);

            $table->text('notes')->nullable();

            $table->string('evidence')->nullable();

            $table->timestamp('retested_at')->nullable();

            $table->timestamps();

            $table->index(['bug_id', 'qa_id']);
            $table->index('bug_fix_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_retests');
    }
};