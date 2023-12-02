import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs';

export default defineConfig(({ mode }) => {
    const env = { ...loadEnv(mode, process.cwd(), ''), ...process.env };

    return {
        server: {
            host: '0.0.0.0',
            port: env.VITE_HMR_PORT ?? 5173,
            strictPort: true,
            https: env.VITE_KEY_FILE && env.VITE_CERT_FILE
                ? {
                    key: fs.readFileSync(env.VITE_KEY_FILE),
                    cert: fs.readFileSync(env.VITE_CERT_FILE)
                }
                : {},
            hmr: {
                protocol: 'ws',
                host: env.VITE_HMR_HOST ?? env.APP_HOST,
                port: env.VITE_HMR_PORT ?? 5173
            },
            watch: {
                usePolling: true,
            }
        },
        plugins: [
            laravel({ input: ['resources/js/app.js'] }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false
                    }
                }
            })
        ],
        resolve: {
            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
                '@utils': path.resolve(__dirname, 'resources/js/utils'),
                '@requests': path.resolve(__dirname, 'resources/js/requests'),
                '@tools': path.resolve(__dirname, 'resources/js/components/tools'),
                '@composables': path.resolve(__dirname, 'resources/js/composables'),
                '@types': path.resolve(__dirname, 'resources/js/types.ts'),
                '@interfaces': path.resolve(__dirname, 'resources/js/interfaces.ts'),
                '@constants': path.resolve(__dirname, 'resources/js/constants.ts'),
                '@symbols': path.resolve(__dirname, 'resources/js/symbols.ts'),
                '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
                '~bootstrap-icons': path.resolve(__dirname, 'node_modules/bootstrap-icons'),
                '~fontawesome': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'),
                '@css': path.resolve(__dirname, 'resources/css'),
                '@fonts': path.resolve(__dirname, 'resources/fonts'),
                '@images': path.resolve(__dirname, 'resources/images')
            }
        }
    };
});
