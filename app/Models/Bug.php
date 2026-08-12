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
        'due_date',
        'attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    public function assignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    // Accessor: $bug->attachment_url
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}