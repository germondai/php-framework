import { defineConfig } from 'vite'

export default defineConfig({
    publicDir: false,

    server: {
        watch: {
            include: ['src/assets/**'],
        },
    },

    build: {
        outDir: 'public/dist/',
        manifest: true,

        rollupOptions: {
            input: {
                /* Adjust these to your assets */
                tailwind: 'src/assets/css/tailwind.css',
                scss: 'src/assets/scss/app.scss',
                js: 'src/assets/js/main.js',
                ts: 'src/assets/ts/app.ts',
            },
        },
    },
})
