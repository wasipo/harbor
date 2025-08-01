import type { Meta, StoryObj } from '@storybook/react';
import { Dashboard } from './Dashboard';

const meta: Meta<typeof Dashboard> = {
    title: 'Features/Identity/Dashboard',
    component: Dashboard,
    parameters: {
        layout: 'fullscreen',
    },
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof meta>;

// システム管理者（IT部門の正社員）
export const SystemAdmin: Story = {
    args: {
        user: {
            id: '01HZKT234567890ABCDEFGHIJK',
            name: 'システム管理 太郎',
            email: 'admin@example.com',
            categories: [
                { id: '1', code: 'it_dept', name: 'IT部門', isPrimary: true },
                { id: '2', code: 'full_time', name: '正社員' },
            ],
            roles: [
                { id: '1', key: 'super_admin', name: 'スーパー管理者権限', description: 'システム全体の管理権限' },
                { id: '2', key: 'system_maintenance', name: 'システム保守権限', description: 'メンテナンス・バックアップ権限' },
            ],
            permissions: [
                { id: '1', key: 'users.read', name: 'ユーザー閲覧' },
                { id: '2', key: 'users.create', name: 'ユーザー作成' },
                { id: '3', key: 'users.update', name: 'ユーザー更新' },
                { id: '4', key: 'users.delete', name: 'ユーザー削除' },
                { id: '5', key: 'system.settings', name: 'システム設定' },
                { id: '6', key: 'system.backup', name: 'バックアップ実行' },
                { id: '7', key: 'system.logs', name: 'システムログ閲覧' },
                { id: '8', key: 'audit.logs', name: '監査ログ閲覧' },
                { id: '9', key: 'audit.reports.create', name: '監査レポート作成' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
        onNavigate: (path) => alert(`Navigate to: ${path}`),
    },
};

// 営業部の正社員
export const SalesEmployee: Story = {
    args: {
        user: {
            id: '01HZKT345678901BCDEFGHIJKL',
            name: '営業 花子',
            email: 'hanako.sales@example.com',
            categories: [
                { id: '1', code: 'sales_dept', name: '営業部', isPrimary: true },
                { id: '2', code: 'full_time', name: '正社員' },
            ],
            roles: [
                { id: '1', key: 'sales_basic', name: '営業基本権限', description: '顧客情報の基本操作' },
            ],
            permissions: [
                { id: '1', key: 'users.read', name: 'ユーザー閲覧' },
                { id: '2', key: 'sales.view', name: '売上情報閲覧' },
                { id: '3', key: 'sales.export', name: '売上データエクスポート' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
        onNavigate: (path) => alert(`Navigate to: ${path}`),
    },
};

// 開発部門の契約社員（デプロイ権限を持つ）
export const ContractEngineer: Story = {
    args: {
        user: {
            name: '開発 次郎',
            email: 'jiro.dev@example.com',
            categories: [
                { id: '1', code: 'dev_dept', name: '開発部門' },
                { id: '2', code: 'contract', name: '契約社員' },
            ],
            roles: [
                { id: '1', key: 'developer_basic', name: '開発者基本権限' },
                { id: '2', key: 'deployment_operator', name: 'デプロイ実行権限' },
            ],
            permissions: [
                { id: '1', key: 'code.read', name: 'ソースコード閲覧' },
                { id: '2', key: 'code.commit', name: 'コード変更コミット' },
                { id: '3', key: 'systems.deploy.staging', name: 'ステージング環境デプロイ' },
                { id: '4', key: 'systems.monitor', name: 'システム監視' },
                { id: '5', key: 'logs.view', name: 'ログ閲覧' },
                { id: '6', key: 'api.debug', name: 'APIデバッグ' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
    },
};

// 新入社員（研修中）
export const NewEmployee: Story = {
    args: {
        user: {
            name: '新入 社員太',
            email: 'rookie@example.com',
            categories: [
                { id: '1', code: 'trainee', name: '研修生' },
                { id: '2', code: 'full_time', name: '正社員' },
            ],
            roles: [
                // まだロールが割り当てられていない
            ],
            permissions: [
                { id: '1', key: 'profile.read', name: '自分のプロフィール閲覧' },
                { id: '2', key: 'training.materials.view', name: '研修資料閲覧' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
    },
};

// 部門責任者（営業部長 兼 内部監査担当）
export const DepartmentManager: Story = {
    args: {
        user: {
            name: '部門長 統括',
            email: 'manager@example.com',
            categories: [
                { id: '1', code: 'sales_dept', name: '営業部' },
                { id: '2', code: 'management', name: '管理職' },
                { id: '3', code: 'full_time', name: '正社員' },
            ],
            roles: [
                { id: '1', key: 'sales_manager', name: '営業部管理者権限' },
                { id: '2', key: 'internal_auditor', name: '内部監査実行権限' },
                { id: '3', key: 'reports_viewer', name: '経営レポート閲覧権限' },
            ],
            permissions: [
                // 営業部管理者として
                { id: '1', key: 'sales.team.manage', name: '営業チーム管理' },
                { id: '2', key: 'sales.targets.set', name: '売上目標設定' },
                { id: '3', key: 'sales.report.export', name: '売上レポートエクスポート' },
                { id: '4', key: 'customers.all.read', name: '全顧客情報閲覧' },
                // 内部監査担当として
                { id: '5', key: 'audit.logs', name: '監査ログ閲覧' },
                { id: '6', key: 'audit.reports.create', name: '監査レポート作成' },
                { id: '7', key: 'compliance.check', name: 'コンプライアンスチェック' },
                { id: '8', key: 'access.history.all', name: '全ユーザーアクセス履歴閲覧' },
                // 経営層として
                { id: '9', key: 'dashboard.executive', name: '経営ダッシュボード閲覧' },
                { id: '10', key: 'reports.financial.view', name: '財務レポート閲覧' },
                { id: '11', key: 'kpi.company.view', name: '全社KPI閲覧' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
    },
};

// パートタイム労働者（人事部アシスタント）
export const PartTimeAssistant: Story = {
    args: {
        user: {
            name: 'パート アシ子',
            email: 'parttime@example.com',
            categories: [
                { id: '1', code: 'hr_dept', name: '人事部' },
                { id: '2', code: 'part_time', name: 'パートタイム' },
            ],
            roles: [
                { id: '1', key: 'hr_assistant', name: '人事アシスタント権限' },
            ],
            permissions: [
                { id: '1', key: 'employees.basic.read', name: '社員基本情報閲覧' },
                { id: '2', key: 'attendance.input', name: '勤怠情報入力' },
                { id: '3', key: 'documents.hr.view', name: '人事書類閲覧' },
                { id: '4', key: 'profile.read', name: '自分のプロフィール閲覧' },
            ],
        },
        onLogout: () => alert('ログアウトがクリックされました'),
    },
};