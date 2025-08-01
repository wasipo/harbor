import React from 'react';
import { useForm, Head, usePage } from '@inertiajs/react';
import { LoginForm, LoginFormData } from '../components/LoginForm';

interface PageProps {
    errors?: {
        email?: string[];
        password?: string[];
    };
    auth: {
        user: any;
    };
}

// Inertia依存を持つコンテナコンポーネント
export function LoginContainer() {
    // ページプロップスからエラーを取得
    const { props } = usePage<PageProps>();
    const pageErrors = props.errors || {};
    
    
    const { data, setData, post, processing, errors } = useForm<LoginFormData>({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (formData: LoginFormData) => {
        post(route('login.attempt'));
    };

    const handleChange = (formData: LoginFormData) => {
        // 各フィールドの変更を反映
        if (formData.email !== data.email) setData('email', formData.email);
        if (formData.password !== data.password) setData('password', formData.password);
        if (formData.remember !== data.remember) setData('remember', formData.remember);
    };

    return (
        <>
            <Head title="ログイン" />
            <LoginForm
                data={data}
                errors={{
                    email: pageErrors.email?.[0],
                    password: pageErrors.password?.[0]
                }}
                processing={processing}
                onSubmit={handleSubmit}
                onChange={handleChange}
            />
        </>
    );
}