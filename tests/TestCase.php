<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * テスト開始時の初期設定
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト環境でSeederを実行
        $this->seed();
    }
}
