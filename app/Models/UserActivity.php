<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model {
    protected $table = 'user_activities';
    protected $fillable = ['user_id', 'activity', 'ip_address', 'user_agent'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
