import {defineConfig} from "vite";
import symfonyPlugin from "vite-plugin-symfony";

export default defineConfig({
	server: {
    	origin: 'http://127.0.0.1:8080',
  	},
  	base:'/',
    plugins: [
        symfonyPlugin(/* options */),
    ],

    build: {
    	manifest:true, 
        rollupOptions: {
            input: "/app.js" /* relative to the root option */
        },
    }
});