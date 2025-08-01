<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Security;

use Illuminate\Support\Facades\Hash;

final class PasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }
}
