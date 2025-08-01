<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Identity;

use App\Application\Identity\LogoutAction;
use App\Presenter\Web\Identity\WebAuthResponseBuilderInterface;
use Illuminate\Http\RedirectResponse;

readonly class LogoutController
{
    public function __construct(
        private LogoutAction $logoutAction,
        private WebAuthResponseBuilderInterface $presenter,
    ) {}

    /**
     * Log the user out
     */
    public function __invoke(): RedirectResponse
    {
        ($this->logoutAction)();

        return $this->presenter->buildLogoutResponse();
    }
}