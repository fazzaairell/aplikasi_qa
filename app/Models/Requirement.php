<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'code', 'description'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }
}