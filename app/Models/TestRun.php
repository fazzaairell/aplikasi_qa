<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRun extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_COMPLETED = 'Completed';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = ['project_id', 'title', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }
}