import './bootstrap';
import '../../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Simple route helper
(window as any).route = function(name: string, params?: any) {
    const routes: Record<string, string> = {
        'login': '/login',
        'login.attempt': '/login',
        'dashboard': '/dashboard',
        'logout': '/logout',
    };
    return routes[name] || '/';
};

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Harbor';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // Map Laravel controller paths to feature-based structure
        const pageMap: Record<string, string> = {
            'Login': 'identity/pages/Login',
            'Dashboard': 'identity/pages/Dashboard',
        };
        
        const pagePath = pageMap[name] || `identity/pages/${name}`;
        
        return resolvePageComponent(
            `../features/${pagePath}.tsx`,
            import.meta.glob('../features/**/pages/*.tsx')
        );
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});