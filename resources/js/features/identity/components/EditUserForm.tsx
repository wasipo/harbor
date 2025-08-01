import React from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from 'react-aria-components';
import { AlertCircle } from 'lucide-react';

export interface UserEditData {
    id: string;
    name: string;
    email: string;
    is_active: boolean;
}

export interface EditUserFormProps {
    user: UserEditData;
    onCancel?: () => void;
}

export function EditUserForm({ user, onCancel }: EditUserFormProps) {
    const { data, setData, put, processing, errors, reset } = useForm({
        name: user.name,
        email: user.email,
        is_active: user.is_active,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('users.update', user.id), {
            onSuccess: () => {
                // おめでとう、保存できたね
            },
        });
    };

    const handleCancel = () => {
        reset();
        onCancel?.();
    };

    return (
      <div className="bg-white rounded-lg border border-gray-200 shadow-sm">
          <div className="px-8 py-6 border-b border-gray-200 p-4">
              <h2 className="text-2xl font-bold text-gray-900">ユーザー情報編集</h2>
              <p className="mt-2 text-sm text-gray-600">ユーザーの基本情報を編集します</p>
          </div>

          <form onSubmit={handleSubmit}>
              <div className="px-8 py-10 space-y-8 p-8">
                  {/* 名前 */}
                  <div className="mb-6">
                      <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                          名前 <span className="text-red-500 text-sm">*</span>
                      </label>
                      <input
                        type="text"
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className={`w-full px-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all ${
                          errors.name
                            ? 'border-red-300 bg-red-50 focus:ring-red-500'
                            : 'border-gray-300 hover:border-gray-400'
                        }`}
                        placeholder="山田 太郎"
                        disabled={processing}
                        aria-invalid={!!errors.name}
                        aria-describedby="name-error"
                      />
                      {errors.name && (
                        <p id="name-error" className="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <AlertCircle className="w-4 h-4 flex-shrink-0" />
                            {errors.name}
                        </p>
                      )}
                  </div>

                  {/* メールアドレス */}
                  <div className="mb-6">
                      <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                          メールアドレス <span className="text-red-500 text-sm">*</span>
                      </label>
                      <input
                        type="email"
                        id="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className={`w-full px-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all ${
                          errors.email
                            ? 'border-red-300 bg-red-50 focus:ring-red-500'
                            : 'border-gray-300 hover:border-gray-400'
                        }`}
                        placeholder="user@example.com"
                        disabled={processing}
                        aria-invalid={!!errors.email}
                        aria-describedby="email-error"
                      />
                      {errors.email && (
                        <p id="email-error" className="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <AlertCircle className="w-4 h-4 flex-shrink-0" />
                            {errors.email}
                        </p>
                      )}
                  </div>

                  {/* アクティブ状態 */}
                  <div>
                      <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
                          <label className="flex items-start cursor-pointer group">
                              <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                                className="mt-1 w-5 h-5 text-gray-900 border-gray-300 rounded focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-150"
                                disabled={processing}
                              />
                              <div className="ml-4">
                                    <span className="text-base font-medium text-gray-900">
                                        アカウントを有効にする
                                    </span>
                                  <p className="mt-1 text-sm text-gray-600">
                                      無効にするとユーザーはログインできなくなります
                                  </p>
                              </div>
                          </label>
                      </div>
                  </div>
              </div>

              {/* ボタン */}
              <div className="px-8 py-6 bg-gray-50 border-t border-gray-200 rounded-b-lg p-6">
                  <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                      <Button
                        type="button"
                        onPress={handleCancel}
                        isDisabled={processing}
                        className="w-full sm:w-auto px-3 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 active:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-150 ease-in-out"
                      >
                          キャンセル
                      </Button>
                      <Button
                        type="submit"
                        isDisabled={processing}
                        className="w-full sm:w-auto px-3 py-3 text-base font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 active:bg-black disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-150 ease-in-out shadow-sm hover:shadow-md"
                      >
                          {processing ? '保存中...' : '変更を保存'}
                      </Button>
                  </div>
              </div>
          </form>
      </div>
    );
}
