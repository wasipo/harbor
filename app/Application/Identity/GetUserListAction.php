<?php

namespace App\Application\Identity;

use App\Adapter\Identity\UserListOutputDTO;
use App\Domain\Identity\User;
use App\Domain\Identity\UserRepositoryInterface;
use Illuminate\Http\Request;

readonly class GetUserListAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(Request $request): UserListOutputDTO
    {
        // todo: 仮実装
        $perPageValue = $request->get('per_page', 15);
        $perPage = is_numeric($perPageValue) ? (int) $perPageValue : 15;

        $searchValue = $request->get('search');
        $search = is_string($searchValue) ? $searchValue : null;

        $categoryValue = $request->get('category');
        $category = is_string($categoryValue) ? $categoryValue : null;

        $paginator = $this->userRepository->findActiveUsers($perPage, $search, $category);

        // Convert paginator to array of domain users
        /** @var array<User> $users */
        $users = $paginator->items();

        return UserListOutputDTO::fromPaginationData(
            users: $users,
            currentPage: $paginator->currentPage(),
            from: $paginator->firstItem(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            to: $paginator->lastItem(),
            total: $paginator->total(),
            firstUrl: $paginator->url(1),
            lastUrl: $paginator->url($paginator->lastPage()),
            prevUrl: $paginator->previousPageUrl(),
            nextUrl: $paginator->nextPageUrl()
        );
    }
}
