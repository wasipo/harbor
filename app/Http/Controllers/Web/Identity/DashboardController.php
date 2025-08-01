<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Identity;

use App\Adapter\Presenter\Web\DashboardPresenter;
use App\Application\Query\DashboardQueryService;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    public function __construct(
        private DashboardQueryService $queryService,
        private DashboardPresenter $presenter,
    ) {}

    public function __invoke(): Response
    {
        $data = $this->queryService->getDashboardData();

        return Inertia::render('Dashboard', $this->presenter->present($data));
    }
}
