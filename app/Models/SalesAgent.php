<?php

namespace App\Models;

use Database\Factories\SalesAgentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A sales agent. Logs in through the "agent" guard and only sees leads assigned to them.
 */
#[Fillable(['employee_name', 'phone', 'email', 'password', 'status'])]
#[Hidden(['password'])]
class SalesAgent extends Authenticatable
{
    /** @use HasFactory<SalesAgentFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * The CRM has no "remember me", so there is no remember_token column.
     *
     * @var string
     */
    protected $rememberTokenName = '';

    /**
     * @return HasMany<Lead, $this>
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_agent_id');
    }

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
