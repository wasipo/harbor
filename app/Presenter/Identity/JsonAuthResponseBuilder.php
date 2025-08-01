<?php

declare(strict_types=1);

namespace App\Presenter\Identity;

use App\Adapter\Identity\AuthOutputDTO;

class JsonAuthResponseBuilder implements AuthResponseBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function build(AuthOutputDTO $dto): array
    {
        return $dto->toArray();
    }
}