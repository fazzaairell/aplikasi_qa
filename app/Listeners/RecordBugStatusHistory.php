<?php

namespace App\Listeners;

use App\Events\BugStatusChanged;
use App\Models\BugHistory;
use Illuminate\Support\Facades\Auth;

class RecordBugStatusHistory
{
    public function handle(BugStatusChanged $event): void
    {
        BugHistory::create([
            'bug_id' => $event->bug->id,
            'changed_by' => Auth::id(),
            'field_name' => 'status',
            'old_value' => $event->oldStatus,
            'new_value' => $event->newStatus,
            'description' => "Status changed from {$event->oldStatus} to {$event->newStatus}",
        ]);
    }
}
