import React from 'react';
import { 
    Form, 
    TextField, 
    Label, 
    Input, 
    Button, 
    Checkbox
} from 'react-aria-components';

export interface LoginFormData {
    email: string;
    password: string;
    remember: boolean;
}

export interface LoginFormProps {
    data: LoginFormData;
    errors?: {
        email?: string;
        password?: string;
    };
    processing?: boolean;
    onSubmit: (data: LoginFormData) => void;
    onChange: (data: LoginFormData) => void;
}

// 純粋なプレゼンテーションコンポーネント
export function LoginForm({ 
    data, 
    errors, 
    processing = false, 
    onSubmit, 
    onChange 
}: LoginFormProps) {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit(data);
    };

    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4">
            <div className="w-full max-w-md">
                {/* Logo & Title */}
                <div className="text-center mb-8">
                    <div className="inline-flex items-center justify-center w-12 h-12 bg-gray-900 rounded-lg mb-4">
                        <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1" />
                        </svg>
                    </div>
                    <h1 className="text-2xl font-medium text-gray-900 mb-1">Harbor</h1>
                    <p className="text-sm text-gray-500">アカウントにログイン</p>
                </div>

                {/* Login Form */}
                <div className="bg-white rounded-lg p-8 shadow-sm border border-gray-200">
                    <Form onSubmit={handleSubmit} className="space-y-5">
                        {/* Error Message */}
                        {errors?.email && (
                            <div className="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md text-sm">
                                {errors.email}
                            </div>
                        )}
                        
                        {/* Email Field */}
                        <TextField className="space-y-1.5">
                            <Label className="block text-sm font-medium text-gray-700">
                                メールアドレス
                            </Label>
                            <Input
                                type="email"
                                value={data.email}
                                onChange={(e) => onChange({...data, email: e.target.value})}
                                className="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-0 focus:border-gray-900 transition-colors"
                                placeholder="your@email.com"
                                autoComplete="email"
                                disabled={processing}
                            />
                        </TextField>

                        {/* Password Field */}
                        <TextField className="space-y-1.5">
                            <Label className="block text-sm font-medium text-gray-700">
                                パスワード
                            </Label>
                            <Input
                                type="password"
                                value={data.password}
                                onChange={(e) => onChange({...data, password: e.target.value})}
                                className="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-0 focus:border-gray-900 transition-colors"
                                placeholder="••••••••"
                                autoComplete="current-password"
                                disabled={processing}
                            />
                        </TextField>

                        {/* Remember & Forgot Password */}
                        <div className="flex items-center justify-between">
                            <label className="flex items-center space-x-2 cursor-pointer">
                                <Checkbox
                                    isSelected={data.remember}
                                    onChange={(checked) => onChange({...data, remember: checked})}
                                    className="w-4 h-4 rounded border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 data-[selected]:bg-gray-900 data-[selected]:border-gray-900 transition-colors"
                                    isDisabled={processing}
                                >
                                    {({isSelected}) => (
                                        <>
                                            {isSelected && (
                                                <svg className="w-3 h-3 text-white mx-auto" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            )}
                                        </>
                                    )}
                                </Checkbox>
                                <span className="text-sm text-gray-600">ログイン状態を保持</span>
                            </label>

                            <a href="#" className="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                パスワードをお忘れですか？
                            </a>
                        </div>

                        {/* Login Button */}
                        <Button
                            type="submit"
                            className="w-full py-2.5 px-4 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            isDisabled={processing}
                        >
                            {processing ? '処理中...' : 'ログイン'}
                        </Button>
                    </Form>
                </div>

                {/* Footer */}
                <div className="text-center mt-8">
                    <p className="text-gray-400 text-xs">
                        © 2025 Harbor. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    );
}