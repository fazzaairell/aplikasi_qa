<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  TestResult extends Model
{
    use HasFactory;

    public const STATUS_UNTESTED = 'Untested';
    public const STATUS_PASSED = 'Passed';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_BLOCKED = 'Blocked';

    public const STATUSES = [
        self::STATUS_UNTESTED,
        self::STATUS_PASSED,
        self::STATUS_FAILED,
        self::STATUS_BLOCKED,
    ];

    protected $fillable = ['test_run_id', 'test_case_id', 'status', 'executed_by'];

    protected static function booted(): void
    {
        static::updated(function (TestResult $testResult) {
            if ($testResult->wasChanged('status')) {
                event(new \App\Events\TestResultStatusChanged(
                    $testResult,
                    $testResult->getOriginal('status'),
                    $testResult->status
                ));
            }
        });
    }

    public function testRun()
    {
        return $this->belongsTo(TestRun::class);
    }

    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function bugs()
    {
        return $this->hasMany(Bug::class);
    }
}