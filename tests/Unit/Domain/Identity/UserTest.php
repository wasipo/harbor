<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\AccountStatus;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserId;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Factories\Domain\Identity\TestUserFactory;

class UserTest extends TestCase
{
    private function createTestUser(): User
    {
        return User::reconstitute(
            id: UserId::fromString('01K06EWBM2JK3M31SE7NWJ50GW'),
            name: new Name('Test User'),
            email: new Email('test@example.com'),
            status: AccountStatus::ACTIVE,
            emailVerifiedAt: CarbonImmutable::parse('2024-01-01 00:00:00'),
            categoryIds: CategoryIdCollection::of(
                UserCategoryId::fromString('01K06EWBM2JK3M31SE7NWJ50G1'),
                UserCategoryId::fromString('01K06EWBM2JK3M31SE7NWJ50G2')
            ),
            roleIds: RoleIdCollection::of(
                RoleId::fromString('01K06EWBM2JK3M31SE7NWJ50H1'),
                RoleId::fromString('01K06EWBM2JK3M31SE7NWJ50H2')
            )
        );
    }

    public function test_正常系_user生成()
    {
        // Arrange
        $id = UserId::create();
        $name = new Name('Test User');
        $email = new Email('test@example.com');
        $status = AccountStatus::ACTIVE;
        $categoryIds = CategoryIdCollection::empty();
        $roleIds = RoleIdCollection::empty();

        // Act
        $user = TestUserFactory::create(
            $id,
            $name,
            $email,
            $status,
            null,
            $categoryIds,
            $roleIds
        );

        // Assert
        $this->assertSame($id, $user->id);
        $this->assertSame($name, $user->name);
        $this->assertSame($email, $user->email);
        $this->assertSame($status, $user->status);
        $this->assertSame($categoryIds, $user->categoryIds);
        $this->assertSame($roleIds, $user->roleIds);
    }

    public function test_正常系_activate動作()
    {
        // Arrange
        $user = $this->createTestUser()->deactivate();

        // Act
        $activatedUser = $user->activate();

        // Assert
        $this->assertTrue($activatedUser->isActive());
        $this->assertFalse($user->isActive()); // 元のオブジェクトは変更されない
        $this->assertSame(AccountStatus::ACTIVE, $activatedUser->status);
    }

    public function test_正常系_deactivate動作()
    {
        // Arrange
        $user = $this->createTestUser();

        // Act
        $deactivatedUser = $user->deactivate();

        // Assert
        $this->assertFalse($deactivatedUser->isActive());
        $this->assertTrue($user->isActive()); // 元のオブジェクトは変更されない
        $this->assertSame(AccountStatus::INACTIVE, $deactivatedUser->status);
    }

    public function test_正常系_suspend動作()
    {
        // Arrange
        $user = $this->createTestUser();

        // Act
        $suspendedUser = $user->suspend();

        // Assert
        $this->assertFalse($suspendedUser->canLogin());
        $this->assertTrue($user->canLogin()); // 元のオブジェクトは変更されない
        $this->assertSame(AccountStatus::SUSPENDED, $suspendedUser->status);
    }

    public function test_正常系_change_name動作()
    {
        // Arrange
        $user = $this->createTestUser();
        $newName = new Name('New User Name');

        // Act
        $renamedUser = $user->changeName($newName);

        // Assert
        $this->assertEquals($newName->value, $renamedUser->name->value);
        $this->assertEquals('Test User', $user->name->value); // 元のオブジェクトは変更されない
    }

    public function test_正常系_change_email動作()
    {
        // Arrange
        $user = $this->createTestUser();
        $newEmail = new Email('new@example.com');

        // Act
        $changedUser = $user->changeEmail($newEmail);

        // Assert
        $this->assertEquals('new@example.com', $changedUser->email->value);
        $this->assertEquals('test@example.com', $user->email->value); // 元のオブジェクトは変更されない
    }

    public function test_正常系_assign_category動作()
    {
        // Arrange
        $user = $this->createTestUser();
        $newCategoryId = UserCategoryId::create();

        // Act
        $updatedUser = $user->assignCategory($newCategoryId);

        // Assert
        $this->assertCount(2, $user->categoryIds); // 元のオブジェクトは変更されない
        $this->assertCount(3, $updatedUser->categoryIds); // 新しいオブジェクトは追加
        $this->assertTrue($updatedUser->categoryIds->contains($newCategoryId));
    }

    public function test_正常系_assign_role動作()
    {
        // Arrange
        $user = $this->createTestUser();
        $newRoleId = RoleId::create();

        // Act
        $updatedUser = $user->assignRole($newRoleId);

        // Assert
        $this->assertCount(2, $user->roleIds); // 元のオブジェクトは変更されない
        $this->assertCount(3, $updatedUser->roleIds); // 新しいオブジェクトは追加
        $this->assertTrue($updatedUser->roleIds->contains($newRoleId));
    }

    public function test_正常系_can_login判定()
    {
        // Arrange
        $activeUser = $this->createTestUser();
        $inactiveUser = $activeUser->deactivate();
        $suspendedUser = $activeUser->suspend();

        // Act & Assert
        $this->assertTrue($activeUser->canLogin());
        $this->assertFalse($inactiveUser->canLogin());
        $this->assertFalse($suspendedUser->canLogin());
    }

    public function test_正常系_is_active判定()
    {
        // Arrange
        $activeUser = $this->createTestUser();
        $inactiveUser = $activeUser->deactivate();
        $suspendedUser = $activeUser->suspend();

        // Act & Assert
        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
        $this->assertFalse($suspendedUser->isActive());
    }

    public function test_正常系_equals比較()
    {
        // Arrange
        $userId = UserId::fromString('01K06EWBM2JK3M31SE7NWJ50GW');
        $user1 = User::reconstitute(
            id: $userId,
            name: new Name('User 1'),
            email: new Email('user1@example.com'),
            status: AccountStatus::ACTIVE
        );
        $user2 = User::reconstitute(
            id: $userId,
            name: new Name('User 2'), // 名前が違っても同じID
            email: new Email('user2@example.com'),
            status: AccountStatus::INACTIVE
        );
        $user3 = User::reconstitute(
            id: UserId::create(), // 異なるID
            name: new Name('User 3'),
            email: new Email('user3@example.com'),
            status: AccountStatus::ACTIVE
        );

        // Act & Assert
        $this->assertTrue($user1->equals($user2)); // 同じID
        $this->assertFalse($user1->equals($user3)); // 異なるID
    }

    public function test_正常系_イミュータブル性確認()
    {
        // Arrange
        $originalUser = $this->createTestUser();
        $newCategoryId = UserCategoryId::create();
        $newRoleId = RoleId::create();

        // Act
        $modifiedUser = $originalUser
            ->changeName(new Name('Modified Name'))
            ->changeEmail(new Email('modified@example.com'))
            ->assignCategory($newCategoryId)
            ->assignRole($newRoleId)
            ->suspend();

        // Assert - 元のオブジェクトは変更されない
        $this->assertEquals('Test User', $originalUser->name->value);
        $this->assertEquals('test@example.com', $originalUser->email->value);
        $this->assertTrue($originalUser->isActive());
        $this->assertCount(2, $originalUser->categoryIds);
        $this->assertCount(2, $originalUser->roleIds);

        // 新しいオブジェクトは変更されている
        $this->assertEquals('Modified Name', $modifiedUser->name->value);
        $this->assertEquals('modified@example.com', $modifiedUser->email->value);
        $this->assertFalse($modifiedUser->canLogin());
        $this->assertCount(3, $modifiedUser->categoryIds);
        $this->assertCount(3, $modifiedUser->roleIds);
    }

    public function test_境界値_空_category_id_collectionで_user生成()
    {
        // Arrange
        $user = User::reconstitute(
            id: UserId::create(),
            name: new Name('Test User'),
            email: new Email('test@example.com'),
            status: AccountStatus::ACTIVE,
            categoryIds: CategoryIdCollection::empty(),
            roleIds: RoleIdCollection::empty()
        );

        // Act & Assert
        $this->assertTrue($user->categoryIds->isEmpty());
        $this->assertTrue($user->roleIds->isEmpty());
    }

    public function test_境界値_大量_category_idで_user生成()
    {
        // Arrange
        $categoryIds = [];
        for ($i = 0; $i < 10; $i++) {
            $categoryIds[] = UserCategoryId::create();
        }
        $user = User::reconstitute(
            id: UserId::create(),
            name: new Name('Test User'),
            email: new Email('test@example.com'),
            status: AccountStatus::ACTIVE,
            categoryIds: CategoryIdCollection::of(...$categoryIds),
            roleIds: RoleIdCollection::empty()
        );

        // Act & Assert
        $this->assertCount(10, $user->categoryIds);
        foreach ($categoryIds as $categoryId) {
            $this->assertTrue($user->categoryIds->contains($categoryId));
        }
    }
}
