<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Menangani penyimpanan & penghapusan file yang diupload user (bug attachment,
 * foto profil, bukti perbaikan developer, dll).
 *
 * Semua file disimpan lewat disk "uploads" (lihat config/filesystems.php),
 * yang root-nya langsung menunjuk ke public/uploads — jadi tidak perlu
 * `php artisan storage:link` (symlink sering bermasalah di Windows), tapi
 * tetap dapat abstraksi Storage:: yang gampang dipindah ke S3/cloud nanti.
 */
class FileUploadService
{
    protected string $disk = 'uploads';

    /**
     * Simpan file ke folder tertentu (misal 'bug-attachments') dengan nama unik.
     * Mengembalikan path relatif (misal 'bug-attachments/173..._abc.jpg') yang
     * disimpan ke kolom database.
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        Storage::disk($this->disk)->putFileAs($folder, $file, $filename);

        return trim($folder, '/') . '/' . $filename;
    }

    /**
     * Hapus file lama berdasarkan path relatif-nya. Aman dipanggil walau
     * file-nya sudah tidak ada / path-nya kosong.
     */
    public function delete(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        if (Storage::disk($this->disk)->exists($relativePath)) {
            Storage::disk($this->disk)->delete($relativePath);
        }
    }

    /**
     * Ganti file lama dengan file baru dalam satu langkah: hapus yang lama
     * (kalau ada), simpan yang baru, kembalikan path relatif yang baru.
     */
    public function replace(UploadedFile $newFile, string $folder, ?string $oldRelativePath): string
    {
        $this->delete($oldRelativePath);

        return $this->store($newFile, $folder);
    }
}
