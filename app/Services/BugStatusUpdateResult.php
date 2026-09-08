<?php

namespace App\Services;

use App\Models\Bug;

/**
 * Hasil dari BugStatusService::updateStatus() — dipakai controller untuk
 * memutuskan respons apa yang dikirim (redirect back / JSON), tanpa perlu
 * controller tahu detail alur bisnisnya.
 */
class BugStatusUpdateResult
{
    public function __construct(
        public readonly bool $success,
        public readonly int $statusCode,
        public readonly string $message,
        public readonly Bug $bug,
    ) {
    }

    public static function ok(Bug $bug, string $message, int $statusCode = 200): self
    {
        return new self(true, $statusCode, $message, $bug);
    }

    public static function fail(Bug $bug, string $message, int $statusCode = 403): self
    {
        return new self(false, $statusCode, $message, $bug);
    }
}
