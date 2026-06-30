<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'nomor_induk','name','email','role','password','avatar',
        'jurusan','telepon','bio','locale','last_seen_at',
    ];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['last_seen_at' => 'datetime','password' => 'hashed'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isDosen(): bool { return $this->role === 'dosen'; }
    public function isMahasiswa(): bool { return $this->role === 'mahasiswa'; }

    // Course yang diajar (dosen)
    public function taughtCourses() { return $this->hasMany(Course::class, 'dosen_id'); }
    // Course yang diikuti (mahasiswa)
    public function courses(): BelongsToMany { return $this->belongsToMany(Course::class); }

    public function submissions() { return $this->hasMany(Submission::class); }
    public function appNotifications()
{
    return $this->hasMany(AppNotification::class, 'user_id')->latest();
}

    public function isOnline(): bool {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }
}