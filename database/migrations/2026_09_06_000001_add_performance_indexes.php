<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index tambahan untuk kolom yang sering dipakai di WHERE/filter tapi belum
 * ter-index otomatis lewat foreign key constraint (kolom status itu string
 * biasa, bukan foreign key). Foreign key (project_id, requirement_id, dst)
 * sudah otomatis ter-index oleh MySQL/InnoDB lewat constrained(), jadi
 * tidak diulang di sini.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Bug Tracker & Dashboard Developer sering filter/hitung by status,
        // dan filter "bug milik saya yang statusnya X" (assigned_to + status).
        Schema::table('bugs', function (Blueprint $table) {
            $table->index('status');
            $table->index(['assigned_to', 'status']);
            $table->index('due_date');
        });

        // Comprehensive Report & summary Test Run sering hitung jumlah per
        // status, baik global maupun per test run (test_run_id + status).
        Schema::table('test_results', function (Blueprint $table) {
            $table->index('status');
            $table->index(['test_run_id', 'status']);
        });

        // Halaman Test Runs & dashboard filter test run yang masih Active.
        Schema::table('test_runs', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['assigned_to', 'status']);
            $table->dropIndex(['due_date']);
        });

        Schema::table('test_results', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['test_run_id', 'status']);
        });

        Schema::table('test_runs', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
