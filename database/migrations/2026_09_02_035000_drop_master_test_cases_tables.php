<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('master_test_case_test_suite');
        Schema::dropIfExists('master_test_cases');
    }

    public function down(): void
    {
        // Tidak perlu rollback
    }
};