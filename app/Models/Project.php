<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

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
}