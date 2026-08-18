<?php

namespace App\Events;

use App\Models\TestResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestResultStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public TestResult $testResult,
        public string $oldStatus,
        public string $newStatus,
    ) {
        //
    }
}
