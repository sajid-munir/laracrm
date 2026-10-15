<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lead_id', 'added_by', 'admin_id', 'agent_id', 'note'])]
class LeadNote extends Model
{
    const UPDATED_AT = null;

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return BelongsTo<SalesAgent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class, 'agent_id');
    }

    /**
     * Name of whoever wrote the note, falling back to the role if that account was deleted.
     */
    public function authorName(): string
    {
        $name = $this->added_by === 'Admin' ? $this->admin?->name : $this->agent?->employee_name;

        return $name ?: ($this->added_by === 'Admin' ? 'Admin' : 'Agent');
    }

    /**
     * Shape used by the notes modal's JavaScript.
     *
     * @return array<string, mixed>
     */
    public function toListArray(): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'author' => $this->authorName(),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
