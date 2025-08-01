<?php

namespace Tests\Feature;

use Tests\TestCase;

class BasicFunctionalityTest extends TestCase
{
    /**
     * Laravel 12アップグレード後の基本動作確認テスト
     */
    public function test_basic_routes_work()
    {
        // 基本的なルーティングが機能するか（未認証時はログインページへリダイレクト）
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // ログインページ（/login）にアクセス
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Harbor');
    }

    public function test_environment_versions()
    {
        // Laravel 12.19.3が実際に動作しているか
        $version = app()->version();
        $this->assertStringStartsWith('12.', $version);
        $this->assertGreaterThanOrEqual('12.19.3', $version);

        // PHP 8.4が動作しているか
        $phpVersion = PHP_VERSION;
        $this->assertStringStartsWith('8.4', $phpVersion);
    }

    public function test_class_loading_works()
    {
        // PSR-4準拠とモデル名前空間移行が完了しているか
        $this->assertTrue(class_exists('App\\Http\\Controllers\\Web\\Identity\\LoginController'));
        $this->assertTrue(class_exists('App\\Models\\User'));
        $this->assertTrue(class_exists('App\\Models\\UserCategory'));

        // 古いApp\Userが存在しないことを確認
        $this->assertFalse(class_exists('App\\User'));
    }
}
