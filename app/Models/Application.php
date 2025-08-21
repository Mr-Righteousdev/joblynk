<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Application extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'resume_path',
        'answers',
        'status',
        'employer_notes',
        'offered_salary',
        'applied_at',
        'viewed_at',
        'responded_at',
        'interview_scheduled_at',
        'interview_location',
        'interview_notes',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'offered_salary' => 'decimal:2',
            'applied_at' => 'datetime',
            'viewed_at' => 'datetime',
            'responded_at' => 'datetime',
            'interview_scheduled_at' => 'datetime',
        ];
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'employer_notes'])
            ->logOnlyDirty();
    }

    // Relationships
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    public function scopeHired($query)
    {
        return $query->where('status', 'hired');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function markAsViewed(): void
    {
        if (!$this->viewed_at) {
            $this->update(['viewed_at' => now()]);
        }
    }

    public function updateStatus(string $status, string $notes = null): void
    {
        $this->update([
            'status' => $status,
            'employer_notes' => $notes,
            'responded_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isHired(): bool
    {
        return $this->status === 'hired';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'shortlisted' => 'purple',
            'interview' => 'indigo',
            'offered' => 'green',
            'hired' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($application) {
            $application->job->incrementApplications();
        });

        static::deleted(function ($application) {
            $application->job->decrementApplications();
        });
    }
}