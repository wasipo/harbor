<?php

namespace App\Http\Requests;

use App\Adapter\Identity\CreateUserCommand;
use App\Http\Traits\HasCommonHeaders;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<Password|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'is_active' => ['boolean'],
            'categories' => ['array'],
            'categories.*' => ['string', 'ulid', 'exists:user_categories,id'],
            'roles' => ['array'],
            'roles.*' => ['string', 'ulid', 'exists:roles,id'],
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

    private function getValidatedName(): string
    {
        $name = $this->validated('name');
        if (!is_string($name)) {
            throw new RuntimeException('Name must be a string');
        }

        return $name;
    }

    private function getValidatedEmail(): string
    {
        $email = $this->validated('email');
        if (!is_string($email)) {
            throw new RuntimeException('Email must be a string');
        }

        return $email;
    }

    private function getValidatedPassword(): string
    {
        $password = $this->validated('password');
        if (!is_string($password)) {
            throw new RuntimeException('Password must be a string');
        }

        return $password;
    }

    private function getValidatedIsActive(): bool
    {
        $isActive = $this->validated('is_active', true);

        return (bool) $isActive;
    }

    /**
     * @return list<string>
     */
    private function getValidatedCategoryIds(): array
    {
        $categories = $this->validated('categories', []);
        if (!is_array($categories)) {
            return [];
        }

        // categories.*はバリデーションでstring型が保証されている
        /** @var list<string> */
        $result = collect($categories)->values()->all();

        return $result;
    }

    /**
     * @return list<string>
     */
    private function getValidatedRoleIds(): array
    {
        $roles = $this->validated('roles', []);
        if (!is_array($roles)) {
            return [];
        }

        // roles.*はバリデーションでstring型が保証されている
        /** @var list<string> */
        $result = collect($roles)->values()->all();

        return $result;
    }

    /**
     * Convert to CreateUserCommand
     */
    public function toCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            name: $this->getValidatedName(),
            email: $this->getValidatedEmail(),
            password: $this->getValidatedPassword(),
            isActive: $this->getValidatedIsActive(),
            categoryIds: $this->getValidatedCategoryIds(),
            roleIds: $this->getValidatedRoleIds()
        );
    }
}
