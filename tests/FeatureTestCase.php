<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class FeatureTestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * フィーチャーテストではSeederを実行する
     */
    protected bool $seed = true;

    /**
     * テスト開始時の初期設定
     */
    protected function setUp(): void
    {
        parent::setUp();

        // フィーチャーテストではSeederを実行
        $this->seed();
    }
}
