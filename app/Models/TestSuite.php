<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSuite extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }

    public function masterTestCases()
    {
        return $this->belongsToMany(MasterTestCase::class, 'master_test_case_test_suite');
    }
}