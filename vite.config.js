import { defineConfig } from "vite";

export default defineConfig({
  server: {
    watch: {
      include: ["src/assets/**"],
    },
  },

  publicDir: false,
  build: {
    outDir: "public/dist/",
    rollupOptions: {
      input: {
        /* Adjust these to your assets */
        css: "src/assets/css/style.css",
        js: "src/assets/js/main.js",
      },
    },
  },
});
