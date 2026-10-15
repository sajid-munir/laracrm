<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * An admin account. Logs in through the "admin" guard.
 */
#[Fillable(['name', 'email', 'password', 'status'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * The CRM has no "remember me", so there is no remember_token column.
     *
     * @var string
     */
    protected $rememberTokenName = '';

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
