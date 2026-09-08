<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_fixes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bug_id')
                ->constrained('bugs')
                ->cascadeOnDelete();

            $table->foreignId('developer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('description');

            $table->string('evidence')->nullable();

            $table->timestamp('fixed_at')->nullable();

            $table->timestamps();

            $table->index(['bug_id', 'developer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_fixes');
    }
};