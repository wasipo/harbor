<?php

namespace App\Http\Controllers\Web\Identity;

use App\Adapter\Identity\LoginCommand;
use App\Application\Identity\LoginAction;
use App\Http\Requests\Auth\LoginRequest;
use App\Presenter\Web\Identity\WebAuthResponseBuilderInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Response;

readonly class LoginController
{
    public function __construct(
        private LoginAction $login,
        private WebAuthResponseBuilderInterface $presenter,
    ) {}

    /**
     * Show the login form
     */
    public function show(): Response
    {
        return $this->presenter->buildLoginFormResponse();
    }

    /**
     * Handle login attempt
     *
     * @throws ValidationException
     */
    public function attempt(LoginRequest $request): RedirectResponse
    {
        $command = LoginCommand::fromRequest($request->validated());

        $result = ($this->login)($command);

        return $this->presenter->buildLoginSuccessResponse($result);
    }
}
