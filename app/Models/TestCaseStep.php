<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_case_id',
        'step_number',
        'description',
        'expected_result',
    ];

    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }
}
