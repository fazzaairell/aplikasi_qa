<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status', 'test_plan'];

    protected $casts = [
        'test_plan' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function testSuites()
    {
        return $this->hasMany(TestSuite::class);
    }

    public function testRuns()
    {
        return $this->hasMany(TestRun::class);
    }

    // Tambahan relasi tembus untuk menghitung Test Case melalui Test Suite
    public function testCases(): HasManyThrough
    {
        return $this->hasManyThrough(TestCase::class, TestSuite::class);
    }
}