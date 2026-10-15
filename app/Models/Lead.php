<?php

namespace App\Models;

use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['lead_title', 'customer_name', 'phone', 'email', 'message', 'assigned_agent_id', 'status', 'is_new'])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    public const STATUSES = ['Won', 'Lost', 'In Process', 'Not Responding'];

    /**
     * @return BelongsTo<SalesAgent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class, 'assigned_agent_id');
    }

    /**
     * @return HasMany<LeadNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    /**
     * @return HasMany<LeadActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(LeadActivityLog::class);
    }

    /**
     * Admins see every lead; an agent sees only the leads assigned to them.
     */
    public function isVisibleTo(User|SalesAgent $account): bool
    {
        return $account instanceof User || $this->assigned_agent_id === $account->id;
    }

    /**
     * Shape used by the lead tables' JavaScript.
     *
     * @return array<string, mixed>
     */
    public function toListArray(): array
    {
        return [
            'id' => $this->id,
            'lead_title' => $this->lead_title,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'message' => $this->message,
            'status' => $this->status,
            'is_new' => $this->is_new,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'assigned_agent_id' => $this->assigned_agent_id,
            'agent_name' => $this->agent?->employee_name,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_new' => 'boolean',
            'assigned_agent_id' => 'integer',
        ];
    }
}
