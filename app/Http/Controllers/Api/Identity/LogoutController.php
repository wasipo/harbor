<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Identity;

use App\Application\Identity\LogoutAction;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class LogoutController
{
    public function __construct(
        private LogoutAction $logoutAction,
    ) {}

    /**
     * ログアウトAPI
     */
    public function __invoke(): JsonResponse
    {
        ($this->logoutAction)();

        return response()->json([
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }
}