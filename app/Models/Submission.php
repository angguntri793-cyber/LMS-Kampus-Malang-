<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'is_completed',
        'submitted_at',
        'nilai',
        'catatan',
        'feedback',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deadlineStatus(): array
    {
        if (!$this->submitted_at) {
            return [
                'late' => false,
                'text' => __('Belum dikumpulkan'),
                'class' => 'text-gray-500',
            ];
        }

        $deadline = $this->assignment->deadline;
        $submitted = $this->submitted_at;

        $diff = $submitted->diff($deadline);
        $isLate = $submitted->gt($deadline);

        $parts = [];
        $locale = app()->getLocale();

        if ($diff->y > 0) $parts[] = $diff->y . ' ' . ($locale == 'id' ? 'tahun' : 'year(s)');
        if ($diff->m > 0) $parts[] = $diff->m . ' ' . ($locale == 'id' ? 'bulan' : 'month(s)');
        if ($diff->d > 0) $parts[] = $diff->d . ' ' . ($locale == 'id' ? 'hari' : 'day(s)');
        if ($diff->h > 0) $parts[] = $diff->h . ' ' . ($locale == 'id' ? 'jam' : 'hour(s)');
        if ($diff->i > 0) $parts[] = $diff->i . ' ' . ($locale == 'id' ? 'menit' : 'minute(s)');

        if (empty($parts)) {
            $durationStr = $diff->s . ' ' . ($locale == 'id' ? 'detik' : 'second(s)');
        } else {
            $durationStr = implode(' ', array_slice($parts, 0, 2));
        }

        if (!$isLate) {
            return [
                'late' => false,
                'text' => $locale == 'id'
                    ? "Mengumpulkan {$durationStr} sebelum deadline"
                    : "Submitted {$durationStr} before deadline",
                'class' => 'text-green-600',
            ];
        } else {
            return [
                'late' => true,
                'text' => $locale == 'id'
                    ? "Terlambat {$durationStr}"
                    : "Late {$durationStr}",
                'class' => 'text-red-600',
            ];
        }
    }

    public function statusInfo(): array
    {
        $status = $this->deadlineStatus();
        return [
            'label' => $status['text'],
            'class' => $status['late'] ? 'badge-danger' : 'badge-success',
            'late' => $status['late'],
        ];
    }
}