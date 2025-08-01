<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Adapter\RegisterUser\AssignCategoriesCommand;
use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;

readonly class AssignCategoriesAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * ユーザーにカテゴリを割り当てる
     */
    public function __invoke(UserId $userId, AssignCategoriesCommand $command): void
    {
        if (empty($command->categoryIds)) {
            return;
        }

        $this->logger->info('Assigning categories to user', [
            'user_id' => $userId->toString(),
            'category_count' => count($command->categoryIds)
        ]);

        $categoryIds = CategoryIdCollection::fromStrings($command->categoryIds);
        $primaryCategoryId = $categoryIds->getPrimaryId();

        $this->userRepository->assignCategories($userId, $categoryIds, $primaryCategoryId);
        
        $this->logger->info('Categories assigned successfully', [
            'user_id' => $userId->toString()
        ]);
    }
}