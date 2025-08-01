<?php

declare(strict_types=1);

namespace App\Adapter\Identity;

use App\Domain\Identity\User;

readonly class UserListOutputDTO
{
    public function __construct(
        /** @var array<int, UserOutputDTO> */
        public readonly array $users,
        /** @var array{current_page: int, from: int|null, last_page: int, per_page: int, to: int|null, total: int} */
        public readonly array $meta,
        /** @var array{first: string|null, last: string|null, prev: string|null, next: string|null} */
        public readonly array $links,
    ) {}

    /**
     * @param  array<User>  $users
     */
    public static function fromPaginationData(
        array $users,
        int $currentPage,
        ?int $from,
        int $lastPage,
        int $perPage,
        ?int $to,
        int $total,
        ?string $firstUrl,
        ?string $lastUrl,
        ?string $prevUrl,
        ?string $nextUrl
    ): self {
        $userDTOs = UserOutputDTO::fromArray($users);

        return new self(
            users: $userDTOs,
            meta: [
                'current_page' => $currentPage,
                'from' => $from,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'to' => $to,
                'total' => $total,
            ],
            links: [
                'first' => $firstUrl,
                'last' => $lastUrl,
                'prev' => $prevUrl,
                'next' => $nextUrl,
            ],
        );
    }

    /**
     * @return array{
     *     data: array<int, array{
     *         id: string,
     *         name: string,
     *         email: string,
     *         is_active: bool,
     *         email_verified_at: string|null,
     *         created_at: string,
     *         updated_at: string,
     *         categories: array<int, array{id: int, code: string, name: string, display_name: string, description: string|null}>,
     *         roles: array<int, array{id: int, name: string, display_name: string, description: string|null, permissions: array<int, string>}>
     *     }>,
     *     meta: array{current_page: int, from: int|null, last_page: int, per_page: int, to: int|null, total: int},
     *     links: array{first: string|null, last: string|null, prev: string|null, next: string|null}
     * }
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(fn ($user) => $user->toArray(), $this->users),
            'meta' => $this->meta,
            'links' => $this->links,
        ];
    }
}
