<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bugs')->whereIn('status', ['Closed', 'Done'])->update([
            'status' => 'Resolved',
        ]);
    }

    public function down(): void
    {
        // Tidak aman mengembalikan Resolved menjadi Closed/Done karena
        // keduanya mempunyai makna berbeda dalam workflow baru.
    }
};
