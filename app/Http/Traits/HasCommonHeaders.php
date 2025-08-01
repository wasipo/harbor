<?php

namespace App\Http\Traits;

trait HasCommonHeaders
{
    /** XSRF ヘッダー */
    public function xsrf(): ?string
    {
        return $this->header('X-XSRF-TOKEN');
    }

    /** 冪等性キー */
    public function idempotencyKey(): ?string
    {
        return $this->header('Idempotency-Key');
    }
}
