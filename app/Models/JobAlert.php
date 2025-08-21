<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'keywords',
        'location',
        'category_id',
        'job_types',
        'salary_min',
        'frequency',
        'is_active',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'job_types' => 'array',
            'salary_min' => 'decimal:2',
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForSending($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('last_sent_at')
                              ->orWhere('frequency', 'immediate')
                              ->orWhere(function ($query) {
                                  $query->where('frequency', 'daily')
                                        ->where('last_sent_at', '<', now()->subDay());
                              })
                              ->orWhere(function ($query) {
                                  $query->where('frequency', 'weekly')
                                        ->where('last_sent_at', '<', now()->subWeek());
                              });
                    });
    }

    // Helper methods
    public function getMatchingJobs()
    {
        $query = Job::active()->published()->notExpired();

        if ($this->keywords) {
            $keywords = explode(' ', $this->keywords);
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                }
            });
        }

        if ($this->location) {
            $query->byLocation($this->location);
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->job_types) {
            $query->byType($this->job_types);
        }

        if ($this->salary_min) {
            $query->where('salary_max', '>=', $this->salary_min);
        }

        return $query->orderBy('published_at', 'desc');
    }

    public function markAsSent(): void
    {
        $this->update(['last_sent_at' => now()]);
    }
}
  