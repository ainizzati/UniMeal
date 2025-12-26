<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'guard',
        'ip_address',
        'user_agent',
        'successful',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Log a login attempt
     */
    public static function logAttempt(string $email, string $guard, bool $successful): void
    {
        self::create([
            'email' => $email,
            'guard' => $guard,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Get failed attempts for email/guard in the last X minutes
     */
    public static function getRecentFailedAttempts(string $email, string $guard, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('guard', $guard)
            ->where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Clear failed attempts for email/guard
     */
    public static function clearFailedAttempts(string $email, string $guard): void
    {
        self::where('email', $email)
            ->where('guard', $guard)
            ->where('successful', false)
            ->delete();
    }
}
