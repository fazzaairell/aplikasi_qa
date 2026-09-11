<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel notifications.
 * Nama class BugNotification dipakai agar tidak bentrok
 * dengan Notification facade bawaan Laravel.
 */
class BugNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'causer_id',
        'bug_id',
        'type',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User yang memicu notifikasi ini (mis. developer yang mengubah
     * status bug), berbeda dengan user() yang merupakan penerimanya.
     */
    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function bug()
    {
        return $this->belongsTo(Bug::class);
    }
}
