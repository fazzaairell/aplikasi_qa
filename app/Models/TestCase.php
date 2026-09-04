<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_suite_id', 
        'requirement_id',  // nullable — boleh null (misal: di-generate dari template)
        'title', 
        'steps', 
        'expected_result', 
        'priority'
    ];

    public function testSuite()
    {
        return $this->belongsTo(TestSuite::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function subSteps()
    {
        return $this->hasMany(TestCaseStep::class);
    }
}