<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Create or update an admin account.
 */
class UserRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'password' => $user ? ['prohibited'] : ['required', 'string', 'min:6'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'A valid email address is required.',
            'email.email' => 'A valid email address is required.',
            'email.unique' => $this->route('user')
                ? 'Another user already uses that email address.'
                : 'A user with that email address already exists.',
            'password.min' => 'Password must be at least 6 characters long.',
            'status.in' => 'Invalid status.',
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->route('user');

                if ($user instanceof User && $user->is($this->user()) && $this->input('status') !== 'Active') {
                    $validator->errors()->add('status', 'You cannot deactivate your own account.');
                }
            },
        ];
    }
}
