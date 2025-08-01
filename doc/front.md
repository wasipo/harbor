## プロジェクト技術構成
Inertia（Laravel+React）

## ディレクトリ構造
```text
  resources/js/
  ├── app/                                    # アプリケーション設定
  │   ├── app.tsx                            # Inertiaエントリーポイント
  │   └── bootstrap.ts                       # 初期化処理
  │
  ├── features/                              # Context（境界づけられたコンテキスト）単位
  │   ├── identity/                          # 認証・ユーザー管理
  │   │   ├── pages/                         # Inertiaページコンポーネント（薄いラッパー）
  │   │   │   ├── Login.tsx                  # ログインページ
  │   │   │   └── Dashboard.tsx              # ダッシュボード
  │   │   ├── containers/                    # Inertia依存を持つコンテナコンポーネント
  │   │   │   ├── LoginContainer.tsx         # ログインロジック
  │   │   │   └── DashboardContainer.tsx     # ダッシュボードロジック
  │   │   ├── components/                    # 純粋なプレゼンテーションコンポーネント
  │   │   │   ├── LoginForm.tsx              # ログインフォームUI
  │   │   │   ├── LoginForm.stories.tsx      # Storybook
  │   │   │   ├── Dashboard.tsx              # ダッシュボードUI
  │   │   │   └── Dashboard.stories.tsx      # Storybook
  │   │   ├── services/                      # APIクライアント層
  │   │   │   └── authService.ts             # 認証API呼び出し
  │   │   ├── hooks/                         # カスタムフック
  │   │   │   ├── useLogin.ts                # ログイン処理
  │   │   │   └── useCurrentUser.ts          # 現在のユーザー情報
  │   │   └── types/                         # DTO型定義（バックエンドと対応）
  │   │       ├── User.ts                    # UserOutputDTO対応
  │   │       ├── AuthResponse.ts            # AuthOutputDTO対応
  │   │       └── LoginRequest.ts            # LoginCommand対応
  │   │
  │   └── access-control/                    # 権限管理
  │       ├── pages/
  │       │   ├── UserList.tsx               # ユーザー一覧
  │       │   └── RoleManagement.tsx         # ロール管理
  │       ├── containers/                    # Inertia依存コンテナ
  │       │   ├── UserListContainer.tsx
  │       │   └── RoleManagementContainer.tsx
  │       ├── components/                    # プレゼンテーションコンポーネント
  │       │   ├── UserTable.tsx
  │       │   ├── UserTable.stories.tsx
  │       │   ├── RoleAssignmentModal.tsx
  │       │   └── RoleAssignmentModal.stories.tsx
  │       ├── services/
  │       │   ├── userService.ts
  │       │   └── roleService.ts
  │       ├── hooks/
  │       │   ├── useUserList.ts
  │       │   └── useRoleAssignment.ts
  │       └── types/
  │           ├── UserCategory.ts
  │           ├── Role.ts
  │           └── Permission.ts
  │
  ├── shared/                                # 共通・汎用コンポーネント
  │   ├── components/
  │   │   ├── ui/                           # 基本UIコンポーネント
  │   │   │   ├── Button.tsx
  │   │   │   ├── Button.stories.tsx
  │   │   │   ├── Input.tsx
  │   │   │   ├── Input.stories.tsx
  │   │   │   ├── Badge.tsx
  │   │   │   ├── Modal.tsx
  │   │   │   └── Toast.tsx
  │   │   └── layouts/                      # レイアウト
  │   │       ├── AppLayout.tsx
  │   │       └── AuthLayout.tsx
  │   ├── hooks/                            # 汎用フック
  │   │   ├── useToast.ts
  │   │   └── useModal.ts
  │   └── styles/                           # 共通スタイル
  │       └── global.css
  │
  ├── libs/                                  # ユーティリティ・設定
  │   ├── api-client.ts                     # Axios設定など
  │   ├── inertia.ts                        # Inertia設定
  │   ├── auth.ts                           # 認証ヘルパー
  │   └── utils/                            # その他ユーティリティ
  │       ├── date.ts
  │       └── validation.ts
  │
  ├── types/                                 # グローバル型定義
  │   ├── index.d.ts                        # 型拡張など
  │   └── generated/                        # OpenAPIから生成される型
  │       └── api.ts
  │
  ├── tests/                                # テスト
  │   ├── features/
  │   │   └── identity/
  │   │       ├── components/
  │   │       │   └── LoginForm.test.tsx
  │   │       └── hooks/
  │   │           └── useLogin.test.ts
  │   └── setup.tsx
  │
  └── stories/                              # Storybookドキュメント
      ├── design-system.mdx                 # デザインシステム説明
      ├── components.mdx                    # コンポーネント一覧
      ├── configure.mdx                     # 設定ガイド
      └── wireframes.mdx                    # ワイヤーフレーム
```

## 設計原則
### Contextを明確化
- features/ 配下は必ずContext単位で分割
- 各Contextは独立して動作可能な設計

### レイヤー責務の分離
- pages: Inertiaページコンポーネント（薄く保つ）
- components: UI表示ロジック
- hooks: ビジネスロジック（UseCase相当）
- services: API通信層
- types: 型定義（DTOと1:1対応）
- stores: 状態管理（必要な場合のみ）

### Backendとの対応
Backend                    →  Frontend
----------------------------------------------
Http/Controllers/Web/*     →  features/*/pages/*
Application/*/Actions      →  features/*/hooks/*
Adapter/*/DTOs            →  features/*/types/*
Presenter/*               →  features/*/services/*

### 共通コンポーネントの利用
- Context固有 → features/*/components/
- 汎用UI → shared/components/ui/
- レイアウト → shared/components/layouts/

## Storybookの活用

### Directory構造
```text
/resources/js/
├── features/identity/components/
│   ├── Dashboard.tsx
│   └── Dashboard.stories.tsx         ← コンポーネントと同じ場所
├── shared/components/ui/
│   ├── Button.tsx
│   └── Button.stories.tsx           ← コンポーネントと同じ場所
/stories/
├── Configure.mdx                    ← Storybook設定
├── components.mdx                   ← コンポーネント概要
├── design-system.mdx                ← デザインシステム
└── wireframes.mdx                   ← ワイヤーフレーム
```

## コンテナ/プレゼンテーションパターン

### 設計理念
Inertia.jsの依存を分離し、テスタブルでStorybookフレンドリーなコンポーネント設計を実現。

### 実装パターン
```typescript
// 1. プレゼンテーションコンポーネント（純粋・テスタブル）
// components/LoginForm.tsx
export interface LoginFormProps {
    data: LoginFormData;
    errors?: Record<string, string>;
    processing?: boolean;
    onSubmit: (data: LoginFormData) => void;
    onChange: (data: LoginFormData) => void;
}

export function LoginForm({ ... }: LoginFormProps) {
    // UIロジックのみ、Inertia依存なし
}

// 2. コンテナコンポーネント（Inertia依存を隔離）
// containers/LoginContainer.tsx
export function LoginContainer() {
    const { data, setData, post, processing, errors } = useForm<LoginFormData>(...);
    return <LoginForm data={data} onSubmit={...} />;
}

// 3. ページコンポーネント（薄いラッパー）
// pages/Login.tsx
export default function Login() {
    return <LoginContainer />;
}
```

### 利点
- **Storybookでの開発**: プレゼンテーションコンポーネントは独立して動作
- **テスト容易性**: 純粋関数としてユニットテスト可能
- **再利用性**: 同じUIを異なるコンテキストで使用可能
- **関心の分離**: フレームワーク固有のロジックとUIの分離

## TypeScript型定義の原則

### Inertia.jsとの共存
```typescript
// ❌ 避けるべき: interface（Inertiaのジェネリック制約と相性が悪い）
interface LoginForm {
    email: string;
    password: string;
}

// ✅ 推奨: type alias
type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};
```

### DTO型定義
バックエンドのDTOと1:1対応を維持：
```typescript
// features/identity/types/User.ts
export type User = {
    id: string;
    name: string;
    email: string;
    categories: UserCategory[];
    roles: Role[];
    permissions: Permission[];
};

// features/access-control/types/UserCategory.ts
export type UserCategory = {
    id: string;
    code: string;  // 'sales_dept', 'full_time' など
    name: string;  // '営業部', '正社員' など
};

// features/access-control/types/Role.ts  
export type Role = {
    id: string;
    key: string;   // 'sales_manager', 'developer_basic' など
    name: string;  // '営業部管理者権限', '開発者基本権限' など
};
```

## React Aria Componentsの活用

### 基本方針
アクセシビリティを最優先に、React Aria Componentsをベースとした実装。

### 実装例
```typescript
import { 
    Form, 
    TextField, 
    Label, 
    Input, 
    Button, 
    Checkbox,
    FieldError
} from 'react-aria-components';

// フォーム実装
<TextField className="space-y-2">
    <Label>メールアドレス</Label>
    <Input
        type="email"
        value={data.email}
        onChange={(e) => onChange({...data, email: e.target.value})}
    />
    {errors?.email && (
        <FieldError>{errors.email}</FieldError>
    )}
</TextField>
```

## UserCategoryとRoleの概念整理

### UserCategory（ユーザー種別）
**「誰か」を表すビジネス的な属性**
- 部門: IT部門、営業部、開発部門、人事部
- 雇用形態: 正社員、契約社員、パートタイム
- 職位: 管理職、研修生

### Role（権限セット）
**「何ができるか」を表す技術的な権限**
- 機能権限: スーパー管理者権限、営業基本権限
- 操作権限: デプロイ実行権限、監査実行権限
- 閲覧権限: 経営レポート閲覧権限

### 実装上の注意
```typescript
// Storybookでのテストデータ例
const user = {
    categories: [
        { code: 'sales_dept', name: '営業部' },      // 部門（属性）
        { code: 'full_time', name: '正社員' }        // 雇用形態（属性）
    ],
    roles: [
        { key: 'sales_basic', name: '営業基本権限' }, // 機能へのアクセス権
        { key: 'report_viewer', name: 'レポート閲覧権限' }
    ]
};
```
