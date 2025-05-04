<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_visit_at',
        'last_visit_at',
        'visit_count',
        'shows_tour'
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
            'first_visit_at' => 'datetime',
            'last_visit_at' => 'datetime',
            'shows_tour' => 'boolean',
        ];
    }

    public function isNewUser(): bool
    {
        // Only consider a user new if they have the shows_tour flag set to true
        // This will be set during registration but not during login
        return (bool) $this->shows_tour;
    }

    public function initializeFirstVisit(): void
    {
        if ($this->visit_count > 0) {
            return; // Already initialized
        }

        $now = now();
        $this->first_visit_at = $now;
        $this->last_visit_at = $now;
        $this->visit_count = 1;
        $this->save();
    }

    public function trackVisit(): void
    {
        $now = now();
        
        if (!$this->first_visit_at) {
            $this->first_visit_at = $now;
            $this->last_visit_at = $now;
            $this->visit_count = 1;
            $this->save();
            return;
        }

        // Always increment the visit count on login
        $this->visit_count++;
        $this->last_visit_at = $now;
        $this->save();
    }

    public function markTourShown(): void
    {
        $this->shows_tour = false;
        $this->save();
    }
}
