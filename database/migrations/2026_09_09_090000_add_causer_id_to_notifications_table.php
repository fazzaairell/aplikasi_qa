<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom causer_id menyimpan siapa yang MEMICU notifikasi
     * (mis. developer yang assign / ubah status), beda dengan
     * user_id yang menyimpan siapa PENERIMA notifikasi.
     * Dipakai untuk menampilkan avatar & profile di halaman notifikasi.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('causer_id')->nullable()->after('user_id');

            $table->foreign('causer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['causer_id']);
            $table->dropColumn('causer_id');
        });
    }
};
