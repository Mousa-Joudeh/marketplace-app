import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

const backend = 'http://127.0.0.1:8000'

export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '^/sanctum/csrf-cookie$': backend,
      '^/login$': backend,
      '^/logout$': backend,
      '^/two-factor-challenge$': backend,
      '^/api(?:/|$)': backend,
    },
  },
})
