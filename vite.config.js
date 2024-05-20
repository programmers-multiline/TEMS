import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import sass from 'vite-plugin-sass'; // Import the sass plugin directly
import path from 'path'; // Add this line to import the path module

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/main.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/codebase/app.js',
            ],
            refresh: true
        })
    ],
    base: "https://phpstack-781350-4569790.cloudwaysapps.com/",
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    // publicDir: 'public' // Specify the correct directory path here
});
