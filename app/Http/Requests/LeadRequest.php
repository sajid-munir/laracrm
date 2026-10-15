<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create or update a lead from the admin panel.
 */
class LeadRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'lead_title' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string'],
            'assigned_agent_id' => ['nullable', 'integer', Rule::exists('sales_agents', 'id')],
            'status' => ['required', Rule::in(Lead::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lead_title.required' => 'Lead title is required.',
            'customer_name.required' => 'Customer name is required.',
            'assigned_agent_id.exists' => 'Selected agent does not exist.',
            'status.in' => 'Invalid status.',
        ];
    }
}
