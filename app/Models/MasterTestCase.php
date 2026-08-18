<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requirement_id',
        'title',
        'steps',
        'expected_result',
        'priority',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function testSuites()
    {
        return $this->belongsToMany(TestSuite::class, 'master_test_case_test_suite');
    }
}
