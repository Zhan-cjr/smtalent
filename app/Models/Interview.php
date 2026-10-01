<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'interviewer_id',
        'scheduled_at',
        'location',
        'notes',
        'strengths',
        'weaknesses',
        'recommendation',
        'average_score',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'average_score' => 'decimal:2',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(InterviewScore::class);
    }

    public function isLockedByOther(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return in_array($this->status, ['scheduled', 'in_progress'])
            && $this->interviewer_id
            && $this->interviewer_id !== $userId;
    }

    public function isLockedByMe(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return in_array($this->status, ['scheduled', 'in_progress'])
            && $this->interviewer_id === $userId;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
