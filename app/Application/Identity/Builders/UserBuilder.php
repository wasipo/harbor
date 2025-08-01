<?php

namespace App\Application\Identity\Builders;

use App\Adapter\Identity\CreateUserCommand;
use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\AccountStatus;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserId;

final class UserBuilder
{
    private function __construct(
        private CreateUserCommand $cmd,
        private ?CategoryIdCollection $categoryIds = null,
        private ?RoleIdCollection $roleIds = null,
    ) {}

    /** 必ずコマンドからスタートさせる */
    public static function fromCommand(CreateUserCommand $cmd): self
    {
        return new self($cmd);
    }

    public function withCategoryIds(CategoryIdCollection $categoryIds): self
    {
        $clone = clone $this;
        $clone->categoryIds = $categoryIds;

        return $clone;
    }

    public function withRoleIds(RoleIdCollection $roleIds): self
    {
        $clone = clone $this;
        $clone->roleIds = $roleIds;

        return $clone;
    }

    public function build(): User
    {
        return User::reconstitute(
            id: UserId::create(),
            name: new Name($this->cmd->name),
            email: new Email($this->cmd->email),
            status: $this->cmd->isActive ? AccountStatus::ACTIVE : AccountStatus::INACTIVE,
            categoryIds: $this->categoryIds ?? CategoryIdCollection::empty(),
            roleIds: $this->roleIds ?? RoleIdCollection::empty(),
        );
    }
}
