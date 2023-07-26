import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react-swc';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  base: '/web/v2/',
  build: {
    outDir: '../web/v2',
    emptyOutDir: true,
    assetsDir: 'assets'
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('/frontend', import.meta.url))
    }
  }
  // server: {
  //   proxy: {
  //     '/': 'http://localhost:8000'
  //   }
  // }
});
