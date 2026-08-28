import { defineConfig } from 'vite'
import { fileURLToPath, URL } from 'node:url'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// The PHP backend is treated as a same-origin JSON API. In dev we proxy
// `/api` to the local PHP server so session/SSO cookies keep working
// exactly like they will in production (single origin, no CORS).
const PHP_ORIGIN = process.env.VITE_PHP_ORIGIN ?? 'http://localhost:8000'

export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: PHP_ORIGIN,
        changeOrigin: true,
      },
    },
  },
  build: {
    // Emit the SPA build where PHP can serve it from. Adjust to taste.
    outDir: '../public/app',
    emptyOutDir: true,
  },
})
