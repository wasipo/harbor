<?php

namespace App\Application\Identity;

use App\Adapter\Identity\UserListOutputDTO;
use App\Domain\Identity\UserRepositoryInterface;
use Illuminate\Http\Request;

readonly class GetUserListAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(Request $request): UserListOutputDTO
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $category = $request->get('category');

        $users = $this->userRepository->findActiveUsers($perPage, $search, $category);

        return UserListOutputDTO::fromPaginator($users);
    }
}
