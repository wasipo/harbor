<?php

namespace App\Domain\AccessControl\Category;

readonly class IdentityUserCategory
{
    public function __construct(
        public UserCategoryId $id,
        public string $code,
        public string $name,
        public bool $isActive
    ) {}

    // Domain behaviors
    public function activate(): self
    {
        return new self(
            $this->id,
            $this->code,
            $this->name,
            true
        );
    }

    public function deactivate(): self
    {
        return new self(
            $this->id,
            $this->code,
            $this->name,
            false
        );
    }

    public function changeName(string $name): self
    {
        return new self(
            $this->id,
            $this->code,
            $name,
            $this->isActive
        );
    }

    // Equality
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
