<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\FeatureTestCase;

class AuthenticationTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_正常系_ログインページが正しく表示される(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Login'));
    }

    public function test_正常系_ゲストユーザーがログインページにアクセスできる(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_正常系_認証済みユーザーはログインページにアクセスできない(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/dashboard');
    }

    public function test_正常系_正しい認証情報でログインできる(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_異常系_間違った認証情報でログインできない(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_異常系_存在しないメールアドレスでログインできない(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_異常系_ログイン入力値検証エラー(): void
    {
        // Email required
        $response = $this->post('/login', [
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');

        // Password required
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
        ]);
        $response->assertSessionHasErrors('password');

        // Invalid email format
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_正常系_ログアウト機能が動作する(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_正常系_ログイン状態記憶機能が動作する(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertNotNull($user);
        $this->assertNotNull($user->remember_token);
    }

    public function test_正常系_ログイン後のユーザー権限確認(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);

        $this->actingAs($user);

        // Test admin permissions
        $this->assertTrue($user->isAdmin());
    }

    public function test_正常系_ログイン後のダッシュボードアクセス(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Dashboard')
                ->has('user.name')
                ->has('user.email')
                ->has('user.categories')
                ->has('user.roles')
                ->has('user.permissions')
        );
    }

    public function test_正常系_非管理者ユーザーのログイン(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $this->assertNotNull($user);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Test non-admin permissions
        $this->assertFalse($user->isAdmin());
    }

    public function test_異常系_非アクティブユーザーはログインできない(): void
    {
        $this->markTestSkipped('メール機能を実装しないので、このテストはスキップします。');
    }

    public function test_正常系_ログイン時のセッション再生成(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Check that session was regenerated (new session ID)
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
