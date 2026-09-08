<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugRetest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bug_id',
        'bug_fix_id',
        'qa_id',
        'result',
        'notes',
        'evidence',
        'retested_at',
    ];

    protected $casts = [
        'retested_at' => 'datetime',
    ];

    public function bug(): BelongsTo
    {
        return $this->belongsTo(Bug::class);
    }

    public function bugFix(): BelongsTo
    {
        return $this->belongsTo(BugFix::class);
    }

    public function qa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qa_id');
    }
}