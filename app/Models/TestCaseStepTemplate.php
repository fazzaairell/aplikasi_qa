<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseStepTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_case_template_id',
        'step_number',
        'description',
        'expected_result',
    ];

    public function testCaseTemplate()
    {
        return $this->belongsTo(TestCaseTemplate::class);
    }
}
