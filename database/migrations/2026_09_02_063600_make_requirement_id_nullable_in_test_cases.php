<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan requirement_id nullable agar test case bisa dibuat
     * tanpa requirement (misal: di-generate dari template).
     */
    public function up(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->foreignId('requirement_id')
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->foreignId('requirement_id')
                  ->nullable(false)
                  ->change();
        });
    }
};
