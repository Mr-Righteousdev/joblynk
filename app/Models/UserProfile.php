<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'bio',
        'skills',
        'experience_years',
        'resume_path',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'website_url',
        'availability',
        'expected_salary_min',
        'expected_salary_max',
        'preferred_location',
        'open_to_remote',
        'date_of_birth',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'experience_years' => 'integer',
            'expected_salary_min' => 'decimal:2',
            'expected_salary_max' => 'decimal:2',
            'open_to_remote' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }

    public function scopeOpenToRemote($query)
    {
        return $query->where('open_to_remote', true);
    }

    // Helper methods
    public function getFullAddressAttribute(): string
    {
        $address = collect([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ])->filter()->implode(', ');

        return $address;
    }

    public function isAvailable(): bool
    {
        return $this->availability === 'available';
    }
}
