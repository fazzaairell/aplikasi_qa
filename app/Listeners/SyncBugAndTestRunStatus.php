<?php

namespace App\Listeners;

use App\Events\TestResultStatusChanged;
use App\Models\TestResult;
use App\Models\TestRun;

class SyncBugAndTestRunStatus
{
    /**
     * TestResult hanya bertanggung jawab atas lifecycle Test Run.
     * Status Bug tidak lagi diubah dari sini agar tidak bentrok dengan
     * workflow Bug: Done in Review -> QA Retest -> Resolved/Reopened.
     */
    public function handle(TestResultStatusChanged $event): void
    {
        $testResult = $event->testResult;
        $run = $testResult->testRun;

        if (! $run) {
            return;
        }

        $allDone = $run->testResults()
            ->whereNotIn('status', [
                TestResult::STATUS_PASSED,
                TestResult::STATUS_FAILED,
                TestResult::STATUS_BLOCKED,
            ])
            ->doesntExist();

        if ($allDone && $run->status !== TestRun::STATUS_COMPLETED) {
            $run->update(['status' => TestRun::STATUS_COMPLETED]);
        }
    }
}
