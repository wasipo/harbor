<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User management permissions
            [
                'key' => 'users.read',
                'resource' => 'users',
                'action' => 'read',
                'display_name' => 'ユーザー一覧表示',
                'description' => 'ユーザー一覧の閲覧権限',
            ],
            [
                'key' => 'users.write',
                'resource' => 'users',
                'action' => 'write',
                'display_name' => 'ユーザー編集',
                'description' => 'ユーザー情報の作成・編集権限',
            ],
            [
                'key' => 'users.delete',
                'resource' => 'users',
                'action' => 'delete',
                'display_name' => 'ユーザー削除',
                'description' => 'ユーザーの削除権限',
            ],
            
            // Profile permissions
            [
                'key' => 'profile.read',
                'resource' => 'profile',
                'action' => 'read',
                'display_name' => 'プロフィール閲覧',
                'description' => '自分のプロフィールの閲覧権限',
            ],
            [
                'key' => 'profile.write',
                'resource' => 'profile',
                'action' => 'write',
                'display_name' => 'プロフィール編集',
                'description' => '自分のプロフィールの編集権限',
            ],
            
            // System permissions
            [
                'key' => 'system.admin',
                'resource' => 'system',
                'action' => 'admin',
                'display_name' => 'システム管理',
                'description' => 'システム全体の管理権限',
            ],
            
            // Technical permissions
            [
                'key' => 'technical.read',
                'resource' => 'technical',
                'action' => 'read',
                'display_name' => '技術情報閲覧',
                'description' => '技術関連情報の閲覧権限',
            ],
            
            // Sales permissions
            [
                'key' => 'sales.read',
                'resource' => 'sales',
                'action' => 'read',
                'display_name' => '営業情報閲覧',
                'description' => '営業関連情報の閲覧権限',
            ],
            
            // Finance permissions
            [
                'key' => 'finance.read',
                'resource' => 'finance',
                'action' => 'read',
                'display_name' => '財務情報閲覧',
                'description' => '財務関連情報の閲覧権限',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::create([
                'id' => str()->ulid()->toString(),
                'key' => $permissionData['key'],
                'resource' => $permissionData['resource'],
                'action' => $permissionData['action'],
                'display_name' => $permissionData['display_name'],
                'description' => $permissionData['description'],
            ]);
        }
    }
}
