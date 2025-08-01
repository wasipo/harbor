<?php

declare(strict_types=1);

namespace Tests\Integration\Application\Query;

use App\Adapter\AccessControl\Category\CategoryDataDTO;
use App\Adapter\AccessControl\Permission\PermissionDataDTO;
use App\Adapter\AccessControl\Role\RoleDataDTO;
use App\Adapter\Query\DashboardDataDTO;
use App\Application\Query\DashboardQueryService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\UnitTestCase;

class DashboardQueryServiceTest extends UnitTestCase
{
    use RefreshDatabase;

    private DashboardQueryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardQueryService;
    }

    public function test_認証されていない場合は例外を投げる(): void
    {
        // Arrange - 認証なし
        $this->assertGuest();

        // Act & Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('User not authenticated');

        $this->service->getDashboardData();
    }

    public function test_最小限のユーザー情報で_dt_oを返す(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->actingAs($user);

        // Act
        $result = $this->service->getDashboardData();

        // Assert
        $this->assertInstanceOf(DashboardDataDTO::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEmpty($result->categories);
        $this->assertEmpty($result->roles);
        $this->assertEmpty($result->permissions);
    }

    public function test_カテゴリ情報を含む_dt_oを返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category1 = UserCategory::factory()->create([
            'code' => 'engineer',
            'name' => 'エンジニア',
        ]);
        $category2 = UserCategory::factory()->create([
            'code' => 'full_time',
            'name' => '正社員',
        ]);

        $user->activeCategories()->attach($category1, [
            'is_primary' => true,
            'effective_from' => now()->subMonth()->format('Y-m-d'),
        ]);
        $user->activeCategories()->attach($category2, [
            'is_primary' => false,
            'effective_from' => now()->subMonth()->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        // Act
        $result = $this->service->getDashboardData();

        // Assert
        $this->assertCount(2, $result->categories);

        // カテゴリを取得（順番は保証されないので、コードで検索）
        $engineerCategory = collect($result->categories)->firstWhere('code', 'engineer');
        $fullTimeCategory = collect($result->categories)->firstWhere('code', 'full_time');

        $this->assertNotNull($engineerCategory);
        $this->assertInstanceOf(CategoryDataDTO::class, $engineerCategory);
        $this->assertEquals($category1->id, $engineerCategory->id);
        $this->assertEquals('engineer', $engineerCategory->code);
        $this->assertEquals('エンジニア', $engineerCategory->name);
        $this->assertTrue($engineerCategory->isPrimary);

        $this->assertNotNull($fullTimeCategory);
        $this->assertInstanceOf(CategoryDataDTO::class, $fullTimeCategory);
        $this->assertEquals($category2->id, $fullTimeCategory->id);
        $this->assertEquals('full_time', $fullTimeCategory->code);
        $this->assertEquals('正社員', $fullTimeCategory->name);
        $this->assertFalse($fullTimeCategory->isPrimary);
    }

    public function test_ロール情報を含む_dt_oを返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $role1 = Role::factory()->create([
            'name' => 'admin',
            'display_name' => '管理者',
        ]);
        $role2 = Role::factory()->create([
            'name' => 'editor',
            'display_name' => '編集者',
        ]);

        $user->roles()->attach($role1);
        $user->roles()->attach($role2);

        $this->actingAs($user);

        // Act
        $result = $this->service->getDashboardData();

        // Assert
        $this->assertCount(2, $result->roles);

        // ロールを取得（順番は保証されないので、keyで検索）
        $adminRole = collect($result->roles)->firstWhere('key', 'admin');
        $editorRole = collect($result->roles)->firstWhere('key', 'editor');

        $this->assertNotNull($adminRole);
        $this->assertInstanceOf(RoleDataDTO::class, $adminRole);
        $this->assertEquals($role1->id, $adminRole->id);
        $this->assertEquals('admin', $adminRole->key);
        $this->assertEquals('管理者', $adminRole->name);
        $this->assertNull($adminRole->description);

        $this->assertNotNull($editorRole);
        $this->assertInstanceOf(RoleDataDTO::class, $editorRole);
        $this->assertEquals($role2->id, $editorRole->id);
        $this->assertEquals('editor', $editorRole->key);
        $this->assertEquals('編集者', $editorRole->name);
        $this->assertNull($editorRole->description);
    }

    public function test_権限情報を含む_dt_oを返す(): void
    {
        // Arrange
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create([
            'key' => 'users.read',
            'display_name' => 'ユーザー閲覧',
        ]);
        $permission2 = Permission::factory()->create([
            'key' => 'users.create',
            'display_name' => 'ユーザー作成',
        ]);
        $permission3 = Permission::factory()->create([
            'key' => 'posts.read',
            'display_name' => '投稿閲覧',
        ]);
        $permission4 = Permission::factory()->create([
            'key' => 'sales.view',
            'display_name' => '売上閲覧',
        ]);

        // ロール経由の権限
        $role1 = Role::factory()->create(['name' => 'admin']);
        $role1->permissions()->attach([$permission1->id, $permission2->id]);

        $role2 = Role::factory()->create(['name' => 'reader']);
        $role2->permissions()->attach([$permission1->id, $permission3->id]);

        $user->roles()->attach([$role1->id, $role2->id]);

        // カテゴリ経由の権限も追加
        $category = UserCategory::factory()->create(['code' => 'sales']);
        $category->permissions()->attach([$permission1->id, $permission4->id]);

        $user->activeCategories()->attach($category, [
            'is_primary' => true,
            'effective_from' => now()->toDateString(),
        ]);

        $this->actingAs($user);

        // Act
        $result = $this->service->getDashboardData();

        // Assert
        $this->assertCount(4, $result->permissions); // 重複を除いた権限数（users.readは重複）

        $permissionKeys = array_map(fn ($p) => $p->key, $result->permissions);
        $this->assertContains('users.read', $permissionKeys);
        $this->assertContains('users.create', $permissionKeys);
        $this->assertContains('posts.read', $permissionKeys);
        $this->assertContains('sales.view', $permissionKeys); // カテゴリ経由の権限

        foreach ($result->permissions as $permission) {
            $this->assertInstanceOf(PermissionDataDTO::class, $permission);
            $this->assertNotEmpty($permission->id);
            $this->assertNotEmpty($permission->key);
            $this->assertNotEmpty($permission->name);
        }
    }

    public function test_完全なダッシュボードデータを返す(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // カテゴリ設定
        $category = UserCategory::factory()->create([
            'code' => 'manager',
            'name' => 'マネージャー',
        ]);
        $user->activeCategories()->attach($category, [
            'is_primary' => true,
            'effective_from' => now()->subMonth()->format('Y-m-d'),
        ]);

        // ロールと権限設定
        $permission1 = Permission::factory()->create([
            'key' => 'team.manage',
            'display_name' => 'チーム管理',
        ]);
        $permission2 = Permission::factory()->create([
            'key' => 'reports.view',
            'display_name' => 'レポート閲覧',
        ]);

        // ロール経由の権限
        $role = Role::factory()->create([
            'name' => 'team_lead',
            'display_name' => 'チームリード',
        ]);
        $role->permissions()->attach($permission1);
        $user->roles()->attach($role);

        // カテゴリ経由の権限
        $category->permissions()->attach($permission2);

        $this->actingAs($user);

        // Act
        $result = $this->service->getDashboardData();

        // Assert
        $this->assertInstanceOf(DashboardDataDTO::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);

        $this->assertCount(1, $result->categories);
        $this->assertEquals('manager', $result->categories[0]->code);
        $this->assertTrue($result->categories[0]->isPrimary);

        $this->assertCount(1, $result->roles);
        $this->assertEquals('team_lead', $result->roles[0]->key);
        $this->assertNull($result->roles[0]->description);

        $this->assertCount(2, $result->permissions); // ロール経由とカテゴリ経由の権限
        $permissionKeys = array_map(fn ($p) => $p->key, $result->permissions);
        $this->assertContains('team.manage', $permissionKeys);
        $this->assertContains('reports.view', $permissionKeys);
    }
}
