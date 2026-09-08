<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_suite_template_id',
        'test_case_code',  // kode unik test case template, misal: TC-001
        'title',
        'steps',           // nullable — detail ada di sub-step templates
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

    /**
     * Sub step (langkah uji rinci) milik test case template ini.
     */
    public function subStepTemplates()
    {
        return $this->hasMany(TestCaseStepTemplate::class)->orderBy('step_number');
    }
}
