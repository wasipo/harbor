<?php

namespace App\Http\Requests\Auth;

use App\Application\Identity\LoginActionValuesInterface;
use App\Http\Traits\HasCommonHeaders;
use Illuminate\Foundation\Http\FormRequest;

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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    use HasCommonHeaders;

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'password.required' => 'パスワードを入力してください。',
        ];
    }

    // LoginActionValuesInterface implementation
    public function email(): string
    {
        /** @var string */
        return $this->validated('email');
    }

    public function password(): string
    {
        /** @var string */
        return $this->validated('password');
    }

    public function remember(): bool
    {
        return (bool) $this->validated('remember', false);
    }
}
