import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

import { loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  return {
    plugins: [
      laravel({
        input: ['resources/js/app.js', 'resources/css/app.css'],
        refresh: true,
      }),
      vue(),
      tailwindcss(),
    ],
    define: {
      'import.meta.env.VITE_APP_NAME': JSON.stringify(env.APP_NAME),
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      strictPort: true,
      origin: 'http://localhost:5173',
      hmr: {
        host: 'localhost',
        clientPort: 5173,
      },
      cors: {
        origin: [env.APP_URL, 'http://localhost'],
      },
    },
  };
});
