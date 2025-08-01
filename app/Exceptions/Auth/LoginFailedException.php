<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;

class LoginFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct('メールアドレスまたはパスワードが正しくありません。');
    }
}
