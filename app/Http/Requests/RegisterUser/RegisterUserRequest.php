<?php

declare(strict_types=1);

namespace App\Http\Requests\RegisterUser;

use App\Adapter\RegisterUser\AssignCategoriesCommand;
use App\Adapter\RegisterUser\AssignRolesCommand;
use App\Adapter\RegisterUser\CreateUserCommand;
use App\Adapter\RegisterUser\RegisterUserCommand;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 誰でも登録可能（後で権限チェックを追加する場合はここで制御）
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'categoryIds' => ['required', 'array', 'min:1', 'distinct'], // 最低1つ必須、重複不可
            'categoryIds.*' => ['string', 'ulid'], // Laravel 12はulidバリデーション対応
            'roleIds' => ['sometimes', 'array', 'distinct'], // 任意（空配列OK）、重複不可
            'roleIds.*' => ['string', 'ulid'], // Laravel 12はulidバリデーション対応
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => '有効なメールアドレスを入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードが一致しません',
            'categoryIds.required' => 'カテゴリを少なくとも1つ選択してください',
            'categoryIds.min' => 'カテゴリを少なくとも1つ選択してください',
            'categoryIds.distinct' => '同じカテゴリを複数選択することはできません',
            'categoryIds.*.ulid' => '無効なカテゴリIDです',
            'roleIds.distinct' => '同じロールを複数選択することはできません',
            'roleIds.*.ulid' => '無効なロールIDです',
        ];
    }

    /**
     * Convert the validated data to a command
     */
    public function toCommand(): RegisterUserCommand
    {
        $validated = $this->validated();
        
        return new RegisterUserCommand(
            createUser: new CreateUserCommand(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password']
            ),
            assignCategories: new AssignCategoriesCommand(
                categoryIds: $validated['categoryIds'] ?? []
            ),
            assignRoles: new AssignRolesCommand(
                roleIds: $validated['roleIds'] ?? []
            )
        );
    }
}