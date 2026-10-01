<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'name',
        'description',
        'total_questions',
        'duration_minutes',
        'passing_grade',
        'is_randomized',
        'is_options_randomized',
        'show_result_to_candidate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_questions' => 'integer',
            'duration_minutes' => 'integer',
            'passing_grade' => 'decimal:2',
            'is_randomized' => 'boolean',
            'is_options_randomized' => 'boolean',
            'show_result_to_candidate' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_package_questions')
            ->withPivot('order_num')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TestSchedule::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function activeSchedule(): ?TestSchedule
    {
        return $this->schedules()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();
    }

    public function upcomingSchedule(): ?TestSchedule
    {
        return $this->schedules()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->first();
    }

    public function latestSchedule(): ?TestSchedule
    {
        return $this->schedules()
            ->where('is_active', true)
            ->latest('end_time')
            ->first();
    }
}
