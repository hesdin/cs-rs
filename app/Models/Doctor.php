<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'polyclinic',
        'bio',
        'photo_url',
        'is_active',
        'leave_start_date',
        'leave_end_date',
        'leave_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'leave_start_date' => 'date',
            'leave_end_date' => 'date',
        ];
    }

    /**
     * Apakah dokter sedang cuti pada tanggal tertentu (default: hari ini).
     */
    public function isOnLeave(?Carbon $date = null): bool
    {
        $date ??= now()->startOfDay();

        if ($this->leave_start_date === null || $this->leave_end_date === null) {
            return false;
        }

        return $date->between($this->leave_start_date, $this->leave_end_date);
    }

    /**
     * @return HasMany<DoctorSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
