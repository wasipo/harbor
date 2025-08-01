<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\AccessControl\Category;

use App\Domain\AccessControl\Category\UserCategory;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Permission\PermissionIdCollection;
use App\Infrastructure\AccessControl\Category\UserCategoryRepository;
use App\Models\Permission;
use App\Models\UserCategory as EloquentUserCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\UnitTestCase;

class UserCategoryRepositoryTest extends UnitTestCase
{
    use RefreshDatabase;

    private UserCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserCategoryRepository;
    }

    #[Test]
    public function 正常系_新規カテゴリ保存(): void
    {
        // Arrange
        $category = UserCategory::create(
            code: 'admin',
            name: '管理者',
            description: 'システム管理者用カテゴリ',
            isActive: true
        );

        // Act
        $saved = $this->repository->save($category);

        // Assert
        $this->assertInstanceOf(UserCategoryId::class, $saved->id);
        $this->assertEquals('admin', $saved->code);
        $this->assertEquals('管理者', $saved->name);
        $this->assertEquals('システム管理者用カテゴリ', $saved->description);
        $this->assertTrue($saved->isActive);

        $this->assertDatabaseHas('user_categories', [
            'code' => 'admin',
            'name' => '管理者',
            'description' => 'システム管理者用カテゴリ',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function 正常系_既存カテゴリ更新(): void
    {
        // Arrange
        $eloquentCategory = EloquentUserCategory::create([
            'id' => (string) Str::ulid(),
            'code' => 'user',
            'name' => '一般ユーザー',
            'description' => '一般ユーザー用カテゴリ',
            'is_active' => true,
        ]);

        $category = UserCategory::reconstitute(
            id: UserCategoryId::fromString($eloquentCategory->id),
            code: $eloquentCategory->code,
            name: '更新後の名前',
            description: '更新後の説明',
            isActive: false,
            permissionIds: PermissionIdCollection::empty()
        );

        // Act
        $saved = $this->repository->save($category);

        // Assert
        $this->assertEquals($eloquentCategory->id, $saved->id->toString());
        $this->assertEquals('user', $saved->code);
        $this->assertEquals('更新後の名前', $saved->name);
        $this->assertEquals('更新後の説明', $saved->description);
        $this->assertFalse($saved->isActive);

        $this->assertDatabaseHas('user_categories', [
            'id' => $eloquentCategory->id,
            'code' => 'user',
            'name' => '更新後の名前',
            'description' => '更新後の説明',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function 正常系_i_dでカテゴリ取得(): void
    {
        // Arrange
        $eloquentCategory = EloquentUserCategory::create([
            'id' => (string) Str::ulid(),
            'code' => 'manager',
            'name' => 'マネージャー',
            'description' => 'マネージャー用カテゴリ',
            'is_active' => true,
        ]);

        // Act
        $found = $this->repository->findById(UserCategoryId::fromString($eloquentCategory->id));

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals($eloquentCategory->id, $found->id->toString());
        $this->assertEquals('manager', $found->code);
        $this->assertEquals('マネージャー', $found->name);
        $this->assertEquals('マネージャー用カテゴリ', $found->description);
        $this->assertTrue($found->isActive);
    }

    #[Test]
    public function 正常系_存在しない_i_dでnull返却(): void
    {
        // Act
        $found = $this->repository->findById(UserCategoryId::create());

        // Assert
        $this->assertNull($found);
    }

    #[Test]
    public function 正常系_権限付きカテゴリ取得(): void
    {
        // Arrange
        $permission1 = Permission::create([
            'id' => (string) Str::ulid(),
            'key' => 'users.create',
            'resource' => 'users',
            'action' => 'create',
            'display_name' => 'ユーザー作成',
        ]);
        $permission2 = Permission::create([
            'id' => (string) Str::ulid(),
            'key' => 'users.edit',
            'resource' => 'users',
            'action' => 'edit',
            'display_name' => 'ユーザー編集',
        ]);

        $eloquentCategory = EloquentUserCategory::create([
            'id' => (string) Str::ulid(),
            'code' => 'admin',
            'name' => '管理者',
            'description' => '管理者カテゴリ',
            'is_active' => true,
        ]);
        $eloquentCategory->permissions()->attach([$permission1->id, $permission2->id]);

        // Act
        $found = $this->repository->findById(UserCategoryId::fromString($eloquentCategory->id));

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals(2, $found->permissionIds->count());
    }
}
