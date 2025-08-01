<?php

declare(strict_types=1);

namespace App\Adapter\RegisterUser;

readonly class RegisterUserCommand
{
    public function __construct(
        public CreateUserCommand $createUser,
        public AssignCategoriesCommand $assignCategories,
        public AssignRolesCommand $assignRoles,
    ) {}
}