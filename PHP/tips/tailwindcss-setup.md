<!-- @format -->

# How to Setup TailwindCSS in laravel 10

1. Install tailwind amd other dependencies

```
npm install -D tailwindcss postcss autoprefixer
```

2. Initialize tailwind (automatically creates tailwind.config.js etc),

```
npx tailwindcss init
```

3. in tailwind.config.js, add the following

```js
// tailwind.config.js
export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/views/**/**/*.blade.php",
    "./resources/views/**/**/**/*.blade.php",
  ],
  prefix: "tw-",
  screens: {
    xs: "100px",
    sm: "576px",
    md: "768px",
    lg: "992px",
    xl: "1200px",
    "2xl": "1400px",
  },
  theme: {
    extend: {},
  },
  plugins: [],
};
```

4. make sure postcss.config.cjs has the following

```js
// postcss.config.cjs
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

5. make sure vite.config.js has the following

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
  ],
  server: {
    hmr: {
      host: "localhost",
    },
  },
});
```

6. Add tailwind directives to main CSS file (e.g resources/css/app.css )

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

7. Link the css output with `@vite` directive

```php
<head>
// ...others...
 @vite('resources/css/app.css')
</head>
```
