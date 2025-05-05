import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    server: { // DEV
        host: '0.0.0.0',
        port: 7437,
        https: {
          key: fs.readFileSync('/etc/letsencrypt/live/romana.robinmillet.fr/privkey.pem'),
          cert: fs.readFileSync('/etc/letsencrypt/live/romana.robinmillet.fr/fullchain.pem'),
        },
        hmr: {
          protocol: 'wss',
          host: 'romana.robinmillet.fr',
          port: 7437,
        },
        headers: {
          'Access-Control-Allow-Origin': 'https://romana.robinmillet.fr',
        },
        cors: {
          origin: 'https://romana.robinmillet.fr',
          credentials: true,
        },
    },
    /*server: { // PROD
        host: '127.0.0.1',
        port: 7437,
        strictPort: true,
        https: false,
        hmr: {
            host: 'romana.robinmillet.fr',
        },
        origin: 'https://romana.robinmillet.fr',
    },*/
});
