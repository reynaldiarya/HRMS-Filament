<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['super_admin', 'admin']),
            'hr' => $this->hasRole('hr'),
            'employee' => $this->hasRole('employee'),
            default => false,
        };
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'department_id', 'position_id',
        'employee_id', 'phone', 'date_of_birth', 'hire_date',
        'employment_type', 'status', 'salary', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'salary' => 'decimal:2',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    protected static function boot()
    {
        parent::boot();
        // EMP-0001

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $lastEmployee = static::orderBy('id', 'desc')->first();

                $nextNumber = 1;

                if ($lastEmployee && $lastEmployee->employee_id) {
                    if (preg_match('/^EMP-(\d+)$/', $lastEmployee->employee_id, $matches)) {
                        $nextNumber = ((int) $matches[1]) + 1;
                    }
                }

                $employee->employee_id = 'EMP-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
