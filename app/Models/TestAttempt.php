<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'test_package_id',
        'started_at',
        'server_end_time',
        'submitted_at',
        'duration_seconds_used',
        'total_questions',
        'total_answered',
        'total_correct',
        'total_wrong',
        'total_unanswered',
        'score_multiple_choice',
        'aspect_scores_json',
        'is_passed',
        'status',
        'question_order_json',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'server_end_time' => 'datetime',
            'submitted_at' => 'datetime',
            'score_multiple_choice' => 'decimal:2',
            'aspect_scores_json' => 'array',
            'question_order_json' => 'array',
            'is_passed' => 'boolean',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function testPackage(): BelongsTo
    {
        return $this->belongsTo(TestPackage::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->status === 'in_progress' && now()->isAfter($this->server_end_time));
    }
}
