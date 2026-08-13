<?php

// app/Models/Bug.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
    protected $fillable = [
        'test_result_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'reported_by',
        'due_date',
        'finish_date',
        'attachment',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'finish_date' => 'date',
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    public function assignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function reporter()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\BugNotification::class, 'bug_id');
    }

    // Accessor: $bug->attachment_url
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}