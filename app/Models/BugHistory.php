<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'bug_id',
        'changed_by',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    public function bug()
    {
        return $this->belongsTo(Bug::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
