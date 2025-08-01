<?php

namespace Tests\Integration\Infrastructure\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\AccountStatus;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserId;
use App\Infrastructure\Identity\UserRepository;
use App\Infrastructure\Shared\Security\PasswordHasher;
use App\Models\Role;
use App\Models\User as EloquentUser;
use App\Models\UserCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\Domain\Identity\TestUserFactory;
use Tests\UnitTestCase;

class UserRepositoryTest extends UnitTestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $passwordHasher = new PasswordHasher;
        $this->repository = new UserRepository($passwordHasher);
    }

    public function test_正常系_i_dで検索成功(): void
    {
        // Arrange
        $eloquentUser = EloquentUser::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);
        $userId = UserId::fromString('01K12GYPNEQP8K2Q17CJSNVEGZ');

        // Act
        $user = $this->repository->findById($userId);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('01K12GYPNEQP8K2Q17CJSNVEGZ', $user->id->toString());
        $this->assertEquals('Test User', $user->name->value);
        $this->assertEquals('test@example.com', $user->email->value);
    }

    public function test_正常系_存在しない_i_dはnull(): void
    {
        // Arrange
        $nonExistentId = UserId::create();

        // Act
        $user = $this->repository->findById($nonExistentId);

        // Assert
        $this->assertNull($user);
    }

    public function test_正常系_メールアドレスで検索成功(): void
    {
        // Arrange
        $eloquentUser = EloquentUser::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
            'email' => 'test@example.com',
        ]);
        $email = new Email('test@example.com');

        // Act
        $user = $this->repository->findByEmail($email);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->email->value);
    }

    public function test_正常系_存在しないメールアドレスはnull(): void
    {
        // Arrange
        $email = new Email('nonexistent@example.com');

        // Act
        $user = $this->repository->findByEmail($email);

        // Assert
        $this->assertNull($user);
    }

    public function test_正常系_メールアドレス存在チェック(): void
    {
        // Arrange
        EloquentUser::factory()->create(['email' => 'existing@example.com']);
        $existingEmail = new Email('existing@example.com');
        $nonExistingEmail = new Email('nonexisting@example.com');

        // Act & Assert
        $this->assertTrue($this->repository->existsByEmail($existingEmail));
        $this->assertFalse($this->repository->existsByEmail($nonExistingEmail));
    }

    public function test_正常系_ユーザー新規作成(): void
    {
        // Arrange
        $user = TestUserFactory::create(
            name: new Name('New User'),
            email: new Email('new@example.com'),
            status: AccountStatus::ACTIVE
        );
        $password = 'password123';

        // Act
        $savedUser = $this->repository->add($user, $password);

        // Assert
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertEquals('New User', $savedUser->name->value);

        // DB確認 - パスワードも正しく保存されているか
        $this->assertDatabaseHas('users', [
            'id' => $savedUser->id->toString(),
            'name' => 'New User',
            'email' => 'new@example.com',
            'is_active' => true,
        ]);

        $eloquentUser = EloquentUser::find($savedUser->id->toString());
        $this->assertNotNull($eloquentUser);
        $this->assertTrue(password_verify($password, $eloquentUser->password));
    }

    public function test_正常系_ユーザー更新(): void
    {
        // Arrange
        $eloquentUser = EloquentUser::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $updatedUser = TestUserFactory::create(
            id: UserId::fromString('01K12GYPNEQP8K2Q17CJSNVEGZ'),
            name: new Name('Updated Name'),
            email: new Email('updated@example.com'),
            status: AccountStatus::INACTIVE
        );

        // Act
        $savedUser = $this->repository->update($updatedUser);

        // Assert
        $this->assertEquals('Updated Name', $savedUser->name->value);
        $this->assertEquals('updated@example.com', $savedUser->email->value);

        // DB確認
        $this->assertDatabaseHas('users', [
            'id' => $eloquentUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_active' => false,
        ]);
    }

    public function test_正常系_パスワード付き更新(): void
    {
        // Arrange
        $eloquentUser = EloquentUser::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
            'password' => bcrypt('old_password'),
        ]);

        $user = TestUserFactory::create(
            id: UserId::fromString('01K12GYPNEQP8K2Q17CJSNVEGZ')
        );
        $newPassword = 'new_password123';

        // Act
        $savedUser = $this->repository->update($user, $newPassword);

        // Assert
        $eloquentUser->refresh();
        $this->assertTrue(password_verify($newPassword, $eloquentUser->password));
        $this->assertFalse(password_verify('old_password', $eloquentUser->password));
    }

    public function test_正常系_カテゴリ割り当て(): void
    {
        // Arrange
        $user = EloquentUser::factory()->create();
        $category1 = UserCategory::factory()->create();
        $category2 = UserCategory::factory()->create();

        $userId = UserId::fromString($user->id);
        $categoryIds = CategoryIdCollection::fromStrings([
            $category1->id,
            $category2->id,
        ]);
        $primaryCategoryId = UserCategoryId::fromString($category1->id);

        // Act
        $this->repository->assignCategories($userId, $categoryIds, $primaryCategoryId);

        // Assert
        $this->assertDatabaseHas('user_category_assignments', [
            'user_id' => $user->id,
            'category_id' => $category1->id,
            'is_primary' => true,
        ]);
        $this->assertDatabaseHas('user_category_assignments', [
            'user_id' => $user->id,
            'category_id' => $category2->id,
            'is_primary' => false,
        ]);
    }

    public function test_正常系_ロール割り当て(): void
    {
        // Arrange
        $user = EloquentUser::factory()->create();
        $assignedBy = EloquentUser::factory()->create();
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $userId = UserId::fromString($user->id);
        $assignedById = UserId::fromString($assignedBy->id);
        $roleIds = RoleIdCollection::fromStrings([
            $role1->id,
            $role2->id,
        ]);

        // Act
        $this->repository->assignRoles($userId, $roleIds, $assignedById);

        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role1->id,
            'assigned_by' => $assignedBy->id,
        ]);
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role2->id,
            'assigned_by' => $assignedBy->id,
        ]);
    }

    public function test_正常系_削除成功(): void
    {
        // Arrange
        $eloquentUser = EloquentUser::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
        ]);
        $user = TestUserFactory::create(
            id: UserId::fromString('01K12GYPNEQP8K2Q17CJSNVEGZ')
        );

        // Act
        $result = $this->repository->delete($user);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $eloquentUser->id]);
    }

    public function test_正常系_存在しないユーザー削除はfalse(): void
    {
        // Arrange
        $user = TestUserFactory::create();

        // Act
        $result = $this->repository->delete($user);

        // Assert
        $this->assertFalse($result);
    }
}
