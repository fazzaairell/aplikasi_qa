<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            // Hapus foreign key lama dulu
            $table->dropForeign(['test_result_id']);
            // Ubah kolom jadi nullable
            $table->unsignedBigInteger('test_result_id')->nullable()->change();
            // Buat ulang foreign key dengan nullable
            $table->foreign('test_result_id')->references('id')->on('test_results')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropForeign(['test_result_id']);
            $table->unsignedBigInteger('test_result_id')->nullable(false)->change();
            $table->foreign('test_result_id')->references('id')->on('test_results')->onDelete('cascade');
        });
    }
};
