import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.ts',
                'resources/js/bootstrap.ts',
                'resources/js/components/packs/success-toast.ts',
                'resources/js/pages/customers/create-edit.ts',
                'resources/js/pages/generic/multiselection.ts',
                'resources/js/pages/generic/poli-multiselection.ts',

                'resources/css/pages/signup.css',
                'resources/css/pages/signin.css',
                'resources/css/pages/register-order.css',
                'resources/css/pages/settings/user/edit.css',
                'resources/css/pages/generic/create.css',
                'resources/css/pages/generic/default.css',
                'resources/css/pages/generic/index.css',
                'resources/css/pages/generic/password.css',
                'resources/css/pages/generic/table.css',

                'resources/css/components/atoms/create-button.css',
                'resources/css/components/atoms/gate/card.css',
                'resources/css/components/atoms/gate/container.css',
                'resources/css/components/atoms/gate/heading.css',
                'resources/css/components/atoms/gate/logo.css',
                'resources/css/components/packs/gate-back-img.css',
                'resources/css/components/packs/header.css',
                'resources/css/components/packs/page-heading-row.css',
                'resources/css/pages/customers/show.css',
                'resources/css/pages/generic/show.css',
            ],
            refresh: true,
        }),
    ],
    server: {
        ...(
            process.env.NODE_ENV === 'development' ? {
                host: '0.0.0.0',
                port: 5173,
                cors: true,
                host: true,
            } : {}
        ),
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
