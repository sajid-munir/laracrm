<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create or update a sales agent.
 */
class AgentRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $agent = $this->route('agent');

        return [
            'employee_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150', Rule::unique('sales_agents', 'email')->ignore($agent)],
            'password' => $agent ? ['prohibited'] : ['required', 'string', 'min:6'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_name.required' => 'Employee name is required.',
            'email.required' => 'A valid email address is required.',
            'email.email' => 'A valid email address is required.',
            'email.unique' => $this->route('agent')
                ? 'Another agent already uses that email address.'
                : 'An agent with that email address already exists.',
            'password.min' => 'Password must be at least 6 characters long.',
            'status.in' => 'Invalid status.',
        ];
    }
}
