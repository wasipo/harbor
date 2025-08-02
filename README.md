# Harbor

「思想が薄まらない設計」の検証と実装を目的とした、スケーラブルな認可基盤プロジェクト。

* ***とりあえずマイページ***と言われた時にすぐ作れるように認可周りのモデリングと実装をまとめました。

> 本プロジェクトでは、Credential（ユーザーの認証・属性）とActor（文脈における主体）の分離を意図しています。
> 
> 実際の運用では、「このユーザーはこの場面で何者か？」を定義するActor構造を追加する必要があります。

## 🛠 技術スタック

- **Backend**: Laravel 11 + PHP 8.4
- **Frontend**: React 18 + TypeScript + Inertia.js 2.0
- **CSS**: Tailwind CSS v4
- **Architecture**: DDD (Domain-Driven Design) + Clean Architecture
- **Database**: MySQL 8.4

## 📁 プロジェクト構造

### Backend (Clean Architecture)

```
app/
├── Domain/                    # ドメイン層（ビジネスロジック）
│   ├── Identity/             # 認証・認可の集約
│   ├── AccessControl/        # 権限管理の集約
│   └── Shared/              # 共有Value Object
├── Application/              # アプリケーション層（UseCase）
│   ├── Identity/
│   └── AccessControl/
├── Infrastructure/           # インフラ層（Repository実装）
│   ├── Identity/
│   └── AccessControl/
├── Adapter/                  # アダプタ層（外部との橋渡し）
│   ├── Identity/            # DTO、Command、Policy
│   └── Shared/              # Logger等
├── Presenter/               # プレゼンター層（出力変換）
│   ├── Api/
│   └── Web/
└── Http/                    # プレゼンテーション層
    └── Controllers/
        ├── Api/
        └── Web/
```

### Frontend

```
resources/js/
├── app/                     # Inertiaエントリーポイント
├── features/               # Context単位の機能
│   └── identity/          # 認証関連
│       ├── pages/         # Inertiaページ
│       ├── containers/    # Inertia依存コンテナ
│       ├── components/    # プレゼンテーション
│       └── types/         # TypeScript型定義
├── shared/                 # 共通コンポーネント
│   └── components/
└── types/                  # グローバル型定義
```

## 🚀 セットアップ

### 必要環境

- Docker & Docker Compose
- Node.js 20 LTS

### インストール

#### はじめかた

```bash
# リポジトリのクローン
git clone https://github.com/wasipo/harbor harbor
cd harbor

npm install
make master-fresh

http://localhost:8080 にアクセス
```

#### テストアカウント

マスターデータ投入後、以下のアカウントでログイン可能です（パスワードは全て `password`）：

| メールアドレス | 役職 | ロール | 権限概要 |
|-------------------|---------------------------|--------------|----------------------------------|
| super@example.com | スーパー管理者 | super_admin | 全権限 |
| admin@example.com | 山田太郎（管理者） | admin | ユーザー・ロール管理、レポート閲覧 |
| suzuki@example.com | 鈴木花子（営業マネージャー） | manager | ユーザー閲覧・更新、レポート管理 |
| sato@example.com | 佐藤次郎（開発リーダー） | leader | ユーザー閲覧、レポート閲覧 |
| tanaka@example.com | 田中美咲（経理担当） | member | 自分の情報のみ閲覧・更新 |
| takahashi@example.com | 高橋健（カスタマーサポート） | member | 自分の情報のみ閲覧・更新 |
| ito@example.com | 伊藤さくら（人事担当） | member | 自分の情報のみ閲覧・更新 |
| watanabe@example.com | 渡辺大輔（開発メンバー） | member | 自分の情報のみ閲覧・更新 |
| nakamura@example.com | 中村優子（マーケティング） | member | 自分の情報のみ閲覧・更新 |
| guest@example.com | ゲストユーザー | guest | 自分の情報のみ閲覧 |


以下のコンテナが起動します：
- **harbor_mysql** - MySQL 8.4 (port: 13306)
- **harbor_php** - PHP-FPM 8.4 Alpine
- **harbor_nginx** - Nginx 1.27 Alpine (port: 8080)
- **harbor_node** - Node.js 20 Alpine

> 上記構成は開発用になるので、本番運用する際は別途構成を考えたほうが良いです。

### アクセス

#### Docker環境
- **アプリケーション**: http://localhost:8080
- **Vite開発サーバー**: http://localhost:5173
- **MySQL**: localhost:13306

## 📱 実装済み機能

### 画面

- **ホーム** (`/`) - ログイン/ダッシュボードへのリダイレクト
- **ログイン** (`/login`) - メールアドレスとパスワードによる認証
- **ダッシュボード** (`/dashboard`) - ユーザー情報と権限の表示

### 機能

- ✅ ログイン/ログアウト
- ✅ セッション管理
- ✅ ユーザー情報表示（名前、メール、カテゴリ、ロール、権限）

## 🗄 データベース構造

### テーブル一覧

- `users` - ユーザー基本情報
- `user_categories` - ユーザーカテゴリ（部門、職種など）
- `user_category_assignments` - ユーザーとカテゴリの紐付け
- `roles` - ロール（権限セット）
- `user_roles` - ユーザーとロールの紐付け
- `permissions` - 個別権限
- `role_permissions` - ロールと権限の紐付け
- `category_permissions` - カテゴリと権限の紐付け

```````mermaid
erDiagram
    %% Core User and Auth
    users {
        varchar id PK "ULID primary key"
        varchar name
        varchar email UK
        varchar password
        boolean is_active
        varchar remember_token
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    user_categories {
        varchar id PK "ULID primary key"
        varchar code UK "engineer/sales…"
        varchar name "表示名"
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    user_category_assignments {
        varchar id PK "ULID primary key"
        varchar user_id FK
        varchar category_id FK
        boolean is_primary
        date   effective_from
        date   effective_until
        timestamp created_at
        timestamp updated_at
    }

    permissions {
        varchar id PK "ULID primary key"
        varchar key UK "users.view_all, roles.create等"
        varchar resource "リソース名"
        varchar action "アクション名"
        varchar display_name "表示名"
        text description
        timestamp created_at
        timestamp updated_at
    }

    category_permissions {
        varchar category_id FK "複合主キーの一部"
        varchar permission_id FK "複合主キーの一部"
        timestamp created_at
        timestamp updated_at
    }

    role_permissions {
        varchar role_id FK "複合主キーの一部"
        varchar permission_id FK "複合主キーの一部"
        timestamp created_at
        timestamp updated_at
    }

    roles {
        varchar id PK "ULID primary key"
        varchar name
        varchar display_name "表示名"
        timestamp created_at
        timestamp updated_at
    }

    user_roles {
        varchar id PK "ULID primary key"
        varchar user_id FK
        varchar role_id FK
        timestamp assigned_at "割当日時"
        varchar assigned_by FK "割当者ID"
        timestamp created_at
        timestamp updated_at
    }

    %% Optional domain profile example (実装予定)
    employee_profiles {
        varchar user_id PK  "FK to users.id"
        varchar employee_number UK
        date hire_date
        decimal hourly_rate
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    users ||--o{ user_category_assignments : "has"
    users ||--o{ user_roles               : "owns"
    users ||--o{ employee_profiles        : "extends"

    user_categories ||--o{ user_category_assignments : "assigned to"
    user_categories ||--o{ category_permissions       : "has"

    roles ||--o{ user_roles : "assigned"
    roles ||--o{ role_permissions : "has"

    permissions ||--o{ category_permissions : "granted to categories"
    permissions ||--o{ role_permissions : "granted to roles"
```````

## 🧪 テスト

```bash
# PHPテストの実行
php artisan test

# 特定のテストのみ実行
php artisan test --filter=LoginTest

# フロントエンドテスト
npm run test
npm run test:ui
npm run test:coverage
```

## 🛠️ 開発ツール

### Docker環境（推奨）

```bash
# 初回セットアップ
make install      # 全環境構築（Docker + 依存関係）

# 起動・停止
make up           # コンテナ起動
make down         # コンテナ停止
make restart      # コンテナ再起動
make logs         # ログ確認

# Laravel操作
make migrate      # マイグレーション実行
make seed         # シーダー実行
make fresh        # DB再構築（データ全削除）
```

### コード品質管理

```bash
# Laravel Pint (コードフォーマッター)
make lint         # チェックのみ
make fix          # 自動修正

# PHPStan (静的解析) 
make stan

# PHPテスト
make test         # Docker環境でテスト実行

make master       # マスター環境でのテスト実行

# ローカル環境の場合
composer lint     # Pintチェック
composer fix      # Pint自動修正
composer analyse  # PHPStan実行
composer check    # lint + analyse + test
```

### UI開発

```bash
# Storybook (コンポーネントカタログ)
npm run storybook
npm run build-storybook

# OpenAPI Code Generator
npm run api:gen    # api/openapi.yaml → TypeScript型生成
```

## 📋 開発ガイドライン

### コーディング規約

- **PSR-12**準拠
- **strict_types=1**の使用
- Value Objectの徹底（プリミティブ型の最小化）
- 日本語コメント/テストメソッド名推奨
- **PHPStan**レベルMAX準拠

### アーキテクチャ原則

- **Architecture**: DDD + Clean Architecture  
  * 複雑なドメインの保守性と拡張性を両立。思想と実装を一致させる構成。

## 🔄 今後の実装予定
- [ ] ユーザー管理機能（一覧、作成、編集、削除）
- [ ] 権限チェック機能の実装
- [ ] APIエンドポイント整備
- [ ] プロフィール編集機能
- [ ] パスワード変更機能
- [ ] サンプルActorの追加

## 📄 ライセンス

MIT License

## 📚 参考ドキュメント

- [設計書](doc/back.md) - システム設計の詳細
- [フロントエンド設計](doc/front.md) - UI/UX設計
