<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_suite_template_id',
        'title',
        'steps',
        'expected_result',
        'priority',
    ];

    /**
     * Template test suite yang memiliki test case ini.
     */
    public function testSuiteTemplate()
    {
        return $this->belongsTo(TestSuiteTemplate::class);
    }
}
