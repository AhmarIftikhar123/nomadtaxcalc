import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    "Clash Grotesk",
                    "Plus Jakarta Sans",
                    "Figtree",
                    ...defaultTheme.fontFamily.sans,
                ],
                display: ["Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#22262a",
                light: "#faf8f7",
                dark: "#18191a",
                gray: "#737578",
                "border-gray": "#e0e0e1",
            },
            borderRadius: {
                DEFAULT: "0.5rem",
                lg: "1rem",
                xl: "1.5rem",
                full: "9999px",
            },
        },
    },

    plugins: [forms],
};
