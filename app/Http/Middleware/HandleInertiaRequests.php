<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var array<string, mixed> $parentShared */
        $parentShared = parent::share($request);

        return array_merge($parentShared, [
            'auth' => [
                'user' => $request->user(),
            ],
            'errors' => function () use ($request) {
                $errors = $request->session()->get('errors');
                if ($errors instanceof \Illuminate\Support\ViewErrorBag) {
                    return $errors->getBag('default')->getMessages();
                }

                return (object) [];
            },
        ]);
    }

    public function handle(Request $request, \Closure $next)
    {
        return parent::handle($request, $next);
    }
}
