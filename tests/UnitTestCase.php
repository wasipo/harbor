<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class UnitTestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * 単体テストではSeederを実行しない
     */
    protected bool $seed = false;

    /**
     * テスト開始時の初期設定
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 単体テストではSeederを実行しない
    }
}
