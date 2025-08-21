<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Job extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'location',
        'city',
        'state',
        'country',
        'type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_period',
        'salary_negotiable',
        'status',
        'is_featured',
        'is_urgent',
        'remote_allowed',
        'required_skills',
        'preferred_skills',
        'application_email',
        'application_url',
        'application_instructions',
        'positions_available',
        'views_count',
        'applications_count',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'salary_negotiable' => 'boolean',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'remote_allowed' => 'boolean',
            'required_skills' => 'array',
            'preferred_skills' => 'array',
            'positions_available' => 'integer',
            'views_count' => 'integer',
            'applications_count' => 'integer',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'is_featured'])
            ->logOnlyDirty();
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function savedByUsers()
    {
        return $this->hasMany(SavedJob::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeRemoteAllowed($query)
    {
        return $query->where('remote_allowed', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%")
                     ->orWhere('city', 'like', "%{$location}%");
    }

    public function scopeByType($query, $types)
    {
        if (is_array($types)) {
            return $query->whereIn('type', $types);
        }
        return $query->where('type', $types);
    }

    public function scopeBySalaryRange($query, $minSalary, $maxSalary)
    {
        return $query->where(function ($query) use ($minSalary, $maxSalary) {
            $query->whereBetween('salary_min', [$minSalary, $maxSalary])
                  ->orWhereBetween('salary_max', [$minSalary, $maxSalary])
                  ->orWhere(function ($query) use ($minSalary, $maxSalary) {
                      $query->where('salary_min', '<=', $minSalary)
                            ->where('salary_max', '>=', $maxSalary);
                  });
        });
    }

    // Helper methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    public function decrementApplications(): void
    {
        $this->decrement('applications_count');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canAcceptApplications(): bool
    {
        return $this->isActive() && !$this->isExpired();
    }

    public function getSalaryRangeAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Salary not specified';
        }

        if ($this->salary_negotiable) {
            return 'Negotiable';
        }

        $currency = 'UGX ';
        $period = $this->salary_period === 'yearly' ? '/year' : 
                 ($this->salary_period === 'monthly' ? '/month' : '/hour');

        if ($this->salary_min && $this->salary_max) {
            return $currency . number_format($this->salary_min) . ' - ' . 
                   $currency . number_format($this->salary_max) . $period;
        }

        if ($this->salary_min) {
            return 'From ' . $currency . number_format($this->salary_min) . $period;
        }

        return 'Up to ' . $currency . number_format($this->salary_max) . $period;
    }

    public function getLocationDisplayAttribute(): string
    {
        if ($this->remote_allowed && $this->location) {
            return $this->location . ' (Remote allowed)';
        }
        
        if ($this->remote_allowed) {
            return 'Remote';
        }

        return $this->location ?: 'Location not specified';
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(6);
            }
        });

        static::created(function ($job) {
            if ($job->status === 'active' && !$job->published_at) {
                $job->update(['published_at' => now()]);
            }
        });
    }
}