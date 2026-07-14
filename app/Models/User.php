<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'nomor_induk','name','email','role','password','avatar',
        'jurusan','telepon','bio','alamat','locale','last_seen_at','is_active',
        'prodi_id','kelas_id',
        'last_login_at','last_login_ip','last_login_device','last_login_browser'
    ];
    protected $hidden = ['password','remember_token'];
    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean'
    ];

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

    public function prodi() { return $this->belongsTo(Prodi::class, 'prodi_id'); }
    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function loginHistories() { return $this->hasMany(LoginHistory::class, 'user_id'); }
    public function activities() { return $this->hasMany(UserActivity::class, 'user_id'); }
    public function quizAttempts() { return $this->hasMany(QuizAttempt::class, 'user_id'); }

    public function isOnline(): bool {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function getAvatarUrlAttribute(): string {
        return $this->avatar 
            ? asset('storage/' . $this->avatar) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563EB&color=fff';
    }

    public function logActivity(string $activity): void {
        $this->activities()->create([
            'activity' => $activity,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}