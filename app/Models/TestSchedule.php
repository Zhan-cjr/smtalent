<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_package_id',
        'name',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isOngoing(): bool
    {
        return $this->is_active && now()->between($this->start_time, $this->end_time);
    }

    public function isUpcoming(): bool
    {
        return $this->is_active && now()->isBefore($this->start_time);
    }

    public function isPast(): bool
    {
        return now()->isAfter($this->end_time);
    }

    public function testPackage(): BelongsTo
    {
        return $this->belongsTo(TestPackage::class);
    }
}
