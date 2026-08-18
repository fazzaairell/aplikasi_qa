<?php

namespace App\Listeners;

use App\Events\TestResultStatusChanged;
use App\Models\Bug;
use App\Models\TestRun;

class SyncBugAndTestRunStatus
{
    public function handle(TestResultStatusChanged $event): void
    {
        $testResult = $event->testResult;
        $bug = Bug::where('test_result_id', $testResult->id)->first();

        if ($bug && $event->newStatus === 'Passed') {
            $bug->update(['status' => 'Done']);
        }

        $run = $testResult->testRun;
        if (! $run) {
            return;
        }

        $allDone = $run->testResults()->whereNotIn('status', ['Passed', 'Failed', 'Blocked'])->doesntExist();
        if ($allDone && $run->status !== 'Completed') {
            $run->update(['status' => 'Completed']);
        }
    }
}
