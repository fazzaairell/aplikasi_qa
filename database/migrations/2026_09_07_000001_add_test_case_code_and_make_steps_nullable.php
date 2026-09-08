<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom test_case_code ke test_cases dan test_case_templates,
     * serta buat kolom steps menjadi nullable (steps digantikan sub-steps).
     */
    public function up(): void
    {
        // --- test_cases ---
        Schema::table('test_cases', function (Blueprint $table) {
            // Tambah kode unik per test case (setelah id, sebelum test_suite_id)
            $table->string('test_case_code', 50)->nullable()->after('id');
            // Steps sekarang opsional karena digantikan oleh sub-steps
            $table->text('steps')->nullable()->change();
            // Expected result juga nullable untuk konsistensi
            $table->text('expected_result')->nullable()->change();
        });

        // --- test_case_templates ---
        Schema::table('test_case_templates', function (Blueprint $table) {
            $table->string('test_case_code', 50)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->dropColumn('test_case_code');
        });

        Schema::table('test_case_templates', function (Blueprint $table) {
            $table->dropColumn('test_case_code');
        });
    }
};
