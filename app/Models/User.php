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
        'visit_count'
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
        ];
    }

    public function isNewUser(): bool
    {
        return $this->visit_count <= 1;
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

        // Only increment if last visit was on a different day
        if ($this->last_visit_at->format('Y-m-d') !== $now->format('Y-m-d')) {
            $this->visit_count++;
        }
        
        $this->last_visit_at = $now;
        $this->save();
    }
}
