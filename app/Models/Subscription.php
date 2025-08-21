<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'amount',
        'stripe_subscription_id',
        'features_used',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'amount' => 'decimal:2',
            'features_used' => 'array',
        ];
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function getRemainingDaysAttribute(): int
    {
        return $this->ends_at->diffInDays(now());
    }

    public function canPostJobs(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $jobsPosted = $this->company->jobs()
            ->whereBetween('created_at', [$this->starts_at, now()])
            ->count();

        return $jobsPosted < $this->subscriptionPlan->job_posts_limit;
    }

    public function canFeatureJobs(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $featuredJobs = $this->company->jobs()
            ->where('is_featured', true)
            ->whereBetween('created_at', [$this->starts_at, now()])
            ->count();

        return $featuredJobs < $this->subscriptionPlan->featured_jobs_limit;
    }
}