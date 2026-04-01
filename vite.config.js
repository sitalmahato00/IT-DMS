import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const viteHost = process.env.VITE_HOST ?? '0.0.0.0';
const vitePort = Number(process.env.VITE_PORT ?? 5173);
const viteHmrHost = process.env.VITE_HMR_HOST ?? 'localhost';
const viteHmrProtocol = process.env.VITE_HMR_PROTOCOL ?? 'ws';
const usePolling = process.env.CHOKIDAR_USEPOLLING === 'true';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: viteHost,
        port: vitePort,
        strictPort: true,
        watch: {
            usePolling,
        },
        hmr: {
            host: viteHmrHost,
            protocol: viteHmrProtocol,
        },
    },
});
