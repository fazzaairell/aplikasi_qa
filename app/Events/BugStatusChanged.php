<?php

namespace App\Events;

use App\Models\Bug;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Bug $bug,
        public string $oldStatus,
        public string $newStatus,
    ) {
        //
    }
}
