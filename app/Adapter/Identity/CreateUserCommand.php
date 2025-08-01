<?php

namespace App\Adapter\Identity;

readonly class CreateUserCommand
{
    /**
     * CreateUserCommand constructor.
     *
     * @param  string  $name  User's name
     * @param  string  $email  User's email
     * @param  string  $password  User's password
     * @param  bool  $isActive  User's active status
     * @param  list<string>  $categoryIds  List of category IDs associated with the user
     * @param  list<string>  $roleIds  List of role IDs associated with the user
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isActive,
        public array $categoryIds,
        public array $roleIds
    ) {}
}
