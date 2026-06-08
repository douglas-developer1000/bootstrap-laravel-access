import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.ts',
                'resources/js/bootstrap.ts',
                'resources/js/components/packs/toast.ts',
                'resources/js/components/packs/add-details-field.ts',
                'resources/js/pages/generic/multiselection.ts',
                'resources/js/pages/generic/poli-multiselection.ts',
                'resources/js/pages/generic/masks.ts',
                'resources/js/pages/generic/datepicker.ts',
                'resources/js/pages/stocks/exit.ts',
                'resources/js/pages/stocks/stock-qty-exit.ts',
                'resources/js/pages/stocks/sale-cards-exit.ts',

                'resources/css/pages/signup.css',
                'resources/css/pages/signin.css',
                'resources/css/pages/register-order.css',
                'resources/css/pages/settings/user/edit.css',
                'resources/css/pages/generic/create.css',
                'resources/css/pages/generic/default.css',
                'resources/css/pages/generic/index.css',
                'resources/css/pages/generic/password.css',
                'resources/css/pages/generic/table.css',
                'resources/css/pages/suppliers/index.css',
                'resources/css/pages/suppliers/create.css',
                'resources/css/pages/suppliers/show.css',
                'resources/css/pages/products/categories/show.css',
                'resources/css/pages/stocks/exit.css',
                
                'resources/css/components/atoms/create-button.css',
                'resources/css/components/atoms/form-field-error.css',
                'resources/css/components/atoms/gate/card.css',
                'resources/css/components/atoms/gate/container.css',
                'resources/css/components/atoms/gate/heading.css',
                'resources/css/components/atoms/gate/logo.css',
                'resources/css/components/molecules/accordion-menu.css',
                'resources/css/components/organisms/extract-btn.css',
                'resources/css/components/packs/gate-back-img.css',
                'resources/css/components/packs/header.css',
                'resources/css/components/packs/page-heading-row.css',
                'resources/css/components/packs/add-details-field.css',
                'resources/css/pages/generic/show.css',
                'resources/css/pages/stocks/show.css',
                'resources/css/pages/sales/index.css',
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
