<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
    public const STATUS_OPEN = 'Open';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_DONE_IN_REVIEW = 'Done in Review';
    public const STATUS_RESOLVED = 'Resolved';
    public const STATUS_REOPENED = 'Reopened';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_DONE_IN_REVIEW,
        self::STATUS_RESOLVED,
        self::STATUS_REOPENED,
    ];

    protected $fillable = [
        'test_result_id',
        'title',
        'description',
        'expected_result',
        'status',
        'assigned_to',
        'reported_by',
        'due_date',
        'finish_date',
        'attachment',
        'fix_attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
        'finish_date' => 'date',
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function notifications()
    {
        return $this->hasMany(BugNotification::class, 'bug_id');
    }

    public function histories()
    {
        return $this->hasMany(BugHistory::class, 'bug_id')->orderBy('created_at', 'desc');
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('uploads/' . $this->attachment) : null;
    }

    public function getFixAttachmentUrlAttribute()
    {
        return $this->fix_attachment ? asset('uploads/' . $this->fix_attachment) : null;
    }
}
