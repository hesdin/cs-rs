<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<DoctorSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
