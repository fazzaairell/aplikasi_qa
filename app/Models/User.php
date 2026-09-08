<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'Admin';
    public const ROLE_QA_LEAD = 'QA Lead';
    public const ROLE_QA_TESTER = 'QA Tester';
    public const ROLE_DEVELOPER = 'Developer';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_QA_LEAD,
        self::ROLE_QA_TESTER,
        self::ROLE_DEVELOPER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'photo_path', // Foto profil pengguna
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI ---

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class, 'executed_by');
    }

    public function assignedBugs()
    {
        return $this->hasMany(Bug::class, 'assigned_to');
    }

    // --- HELPER ROLE ---
    // Satu-satunya tempat yang tahu bagaimana cara membandingkan role.
    // Controller, middleware, dan view semua panggil method ini — supaya
    // kalau suatu saat nama/penulisan role berubah, cukup diubah di sini.

    public function hasRole(string ...$roles): bool
    {
        $userRole = strtolower(trim($this->role ?? ''));

        foreach ($roles as $role) {
            if ($userRole === strtolower(trim($role))) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isDeveloper(): bool
    {
        return $this->hasRole(self::ROLE_DEVELOPER);
    }

    public function isQa(): bool
    {
        return $this->hasRole(self::ROLE_QA_LEAD, self::ROLE_QA_TESTER);
    }

    public function isQaTester(): bool
    {
        return $this->hasRole(self::ROLE_QA_TESTER);
    }
}