<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Identity;

use App\Adapter\Identity\LoginCommand;
use App\Application\Identity\LoginAction;
use App\Http\Requests\Api\Identity\LoginRequest;
use App\Presenter\Identity\AuthResponseBuilderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class LoginController
{
    public function __construct(
        private LoginAction $loginAction,
        private AuthResponseBuilderInterface $presenter,
    ) {}

    /**
     * ログインAPI
     *
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $command = LoginCommand::fromRequest($request->validated());

        $result = ($this->loginAction)($command);

        return response()->json($this->presenter->build($result), ResponseAlias::HTTP_OK);
    }
}
