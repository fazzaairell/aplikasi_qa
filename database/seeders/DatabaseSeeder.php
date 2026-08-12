<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestSuite;
use App\Models\TestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Data Users dengan berbagai Role sesuai ERD
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@qa.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);


        $qaTester = User::create([
            'name' => 'Budi Tester',
            'email' => 'tester@qa.com',
            'password' => Hash::make('password'),
            'role' => 'QA Tester',
        ]);


        $developer = User::create([
            'name' => 'Joko Developer',
            'email' => 'dev@qa.com',
            'password' => Hash::make('password'),
            'role' => 'Developer',
        ]);

        // 2. Buat Contoh Proyek QA
        $project = Project::create([
            'name' => 'Aplikasi E-Commerce V1',
            'description' => 'Pengujian modul pembayaran dan keranjang belanja online.',
        ]);

        // 3. Masukkan User ke Proyek (Tabel Pivot project_user)
        // QA Lead, Tester, dan Developer ditugaskan ke proyek ini
        $project->users()->attach([$qaTester->id, $developer->id]);

        // 4. Buat Requirement (Persyaratan Sistem)
        $requirement = Requirement::create([
            'project_id' => $project->id,
            'code' => 'REQ-PAY-01',
            'title' => 'Payment Gateway Integration',
            'description' => 'Pengguna harus dapat melakukan checkout menggunakan metode transfer bank.',
            'due_date' => now()->addDays(30)->toDateString(),
        ]);

        // 5. Buat Test Suite
        $testSuite = TestSuite::create([
            'project_id' => $project->id,
            'name' => 'Modul Checkout & Payment',
        ]);

        // 6. Buat Test Case
        TestCase::create([
            'test_suite_id' => $testSuite->id,
            'requirement_id' => $requirement->id,
            'title' => 'Verifikasi tombol bayar aktif saat saldo mencukupi',
            'steps' => '1. Masuk ke halaman keranjang\n2. Pilih metode transfer bank\n3. Klik tombol Bayar',
            'expected_result' => 'Sistem menampilkan nomor virtual account dan instruksi pembayaran.',
            'priority' => 'High',
        ]);
    }
}