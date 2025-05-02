import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
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
        cors: true,
        headers: {
          'Access-Control-Allow-Origin': 'https://romana.robinmillet.fr',
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
