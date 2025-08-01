<?php

namespace App\Http\Requests;

use App\Adapter\Identity\CreateUserCommand;
use App\Http\Traits\HasCommonHeaders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Infrastructure\Persistence\Eloquent\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'is_active' => ['boolean'],
            'categories' => ['array'],
            'categories.*' => ['integer', 'exists:user_categories,id'],
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    use HasCommonHeaders;

    /**
     * Default values
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ?? true,
        ]);
    }

    /**
     * Convert to CreateUserCommand
     */
    public function toCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            isActive: $this->validated('is_active', true),
            categoryIds: $this->validated('categories', []),
            roleIds: $this->validated('roles', [])
        );
    }
}
