<?php

namespace App\Application\Identity;

interface LoginActionValuesInterface
{
    public function email(): string;

    public function password(): string;

    public function remember(): bool;
}
