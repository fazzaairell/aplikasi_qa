<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSuiteTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    /**
     * User yang membuat template ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Semua test case yang termasuk dalam template ini.
     */
    public function testCaseTemplates()
    {
        return $this->hasMany(TestCaseTemplate::class);
    }
}
