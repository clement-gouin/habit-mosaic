import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs';
import iconsManifest from '@fortawesome/fontawesome-free/metadata/icon-families.json';

function computeIconsData() {
    const output = {};
    Object.keys(iconsManifest).forEach(name => {
        const data = iconsManifest[name];
        output[name] = {
            'search': data.search.terms.concat(...((data.aliases ?? []).names ?? [])),
            'styles': data.familyStylesByLicense.free.map(d => d.style),
        };
    })
    return JSON.stringify(output);
}

function computeIconNames() {
    return JSON.stringify(Object.keys(iconsManifest));
}

function computeIconSearch() {
    const output = {};
    Object.keys(iconsManifest).forEach(name => {
        const terms = iconsManifest[name].search.terms.concat(...((iconsManifest[name].aliases ?? []).names ?? []));
        if (terms.length) {
            output[name] = terms;
        }
    })
    return JSON.stringify(output);
}

function computeIconStyles() {
    const output = {};
    Object.keys(iconsManifest).forEach(name => {
        output[name] = iconsManifest[name].familyStylesByLicense.free.map(d => d.style);
    })
    return JSON.stringify(output);
}

export default defineConfig(({ mode }) => {
    const env = { ...loadEnv(mode, process.cwd(), ''), ...process.env };

    return {
        define: {
            __ICONS__: computeIconNames(),
            __ICON_SEARCHES__: computeIconSearch(),
            __ICON_STYLES__: computeIconStyles(),
        },
        server: {
            host: '0.0.0.0',
            port: env.VITE_HMR_PORT ?? 5173,
            strictPort: true,
            https: env.VITE_KEY_FILE && env.VITE_CERT_FILE
                ? {
                    key: fs.readFileSync(env.VITE_KEY_FILE),
                    cert: fs.readFileSync(env.VITE_CERT_FILE)
                }
                : false,
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
                '~fontawesome': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'),
                '@css': path.resolve(__dirname, 'resources/css'),
                '@fonts': path.resolve(__dirname, 'resources/fonts'),
                '@images': path.resolve(__dirname, 'resources/images')
            }
        },
    };
});
