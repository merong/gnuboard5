import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  define: {
    'process.env.NODE_ENV': JSON.stringify('production'),
  },
  build: {
    lib: {
      entry: resolve(__dirname, 'src/main.jsx'),
      name: 'MoidamShortform',
      formats: ['iife'],
      fileName: () => 'moidam-shortform.js',
      cssFileName: 'moidam-shortform',
    },
    outDir: 'dist',
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        exports: 'named',
        inlineDynamicImports: true,
        assetFileNames: (assetInfo) => {
          const n = assetInfo.name || '';
          if (n.endsWith('.css')) return 'moidam-shortform.css';
          return n;
        },
      },
    },
  },
});
