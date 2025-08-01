<?php

declare(strict_types=1);

namespace App\Adapter\Presenter\Web;

use App\Adapter\Query\DashboardDataDTO;

final class DashboardPresenter
{
    /**
     * ダッシュボード表示用のレスポンスを構築
     *
     * @param DashboardDataDTO $data
     * @return array<string, mixed>
     */
    public function present(DashboardDataDTO $data): array
    {
        return [
            'user' => [
                'id' => $data->id,
                'name' => $data->name,
                'email' => $data->email,
                'categories' => array_map(
                    fn($category) => [
                        'id' => $category->id,
                        'code' => $category->code,
                        'name' => $category->name,
                        'isPrimary' => $category->isPrimary,
                        'permissionKeys' => $category->permissionKeys,
                    ],
                    $data->categories
                ),
                'roles' => array_map(
                    fn($role) => [
                        'id' => $role->id,
                        'key' => $role->key,
                        'name' => $role->name,
                        'description' => $role->description,
                        'permissionKeys' => $role->permissionKeys,
                    ],
                    $data->roles
                ),
                'permissions' => array_map(
                    fn($permission) => [
                        'id' => $permission->id,
                        'key' => $permission->key,
                        'name' => $permission->name,
                    ],
                    $data->permissions
                ),
            ],
        ];
    }
}