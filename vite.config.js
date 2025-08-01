import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app/app.tsx',
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined, // チャンク分離を無効化
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '0.0.0.0',
        port: parseInt(process.env.VITE_PORT || '5174'),
        hmr: {
            host: 'localhost',
            port: parseInt(process.env.VITE_PORT || '5174'),
        },
        strictPort: true,
        watch: {
            usePolling: process.env.VITE_USE_POLLING === 'true',
        },
    },
});