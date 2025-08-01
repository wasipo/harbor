<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Identity;

use App\Application\Identity\LoginActionValuesInterface;
use Illuminate\Foundation\Http\FormRequest;
use RuntimeException;

class LoginRequest extends FormRequest implements LoginActionValuesInterface
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom error messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'password.required' => 'パスワードは必須です。',
        ];
    }

    public function email(): string
    {
        $email = $this->validated('email');
        if (!is_string($email)) {
            throw new RuntimeException('Email must be a string');
        }

        return $email;
    }

    public function password(): string
    {
        $password = $this->validated('password');
        if (!is_string($password)) {
            throw new RuntimeException('Password must be a string');
        }

        return $password;
    }

    public function remember(): bool
    {
        $remember = $this->validated('remember', false);

        return (bool) $remember;
    }
}
