<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A one-time password reset link for an admin or an agent.
 * The token column stores a SHA-256 hash; the plain token only appears in the link.
 */
#[Fillable(['role', 'email', 'token', 'expires_at', 'used'])]
class PasswordReset extends Model
{
    const UPDATED_AT = null;

    /**
     * Find an unused, unexpired reset by its plain token.
     */
    public static function findValid(string $plainToken): ?self
    {
        if ($plainToken === '') {
            return null;
        }

        return static::query()
            ->where('token', hash('sha256', $plainToken))
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * The account (admin or agent) this reset belongs to.
     *
     * @return Builder<User>|Builder<SalesAgent>
     */
    public function accountQuery(): Builder
    {
        return $this->role === 'Agent'
            ? SalesAgent::query()->where('email', $this->email)
            : User::query()->where('email', $this->email);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
        ];
    }
}
