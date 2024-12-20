import "../css/app.css";
import "@fortawesome/fontawesome-free/css/all.min.css";

import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";

const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });

createInertiaApp({
    resolve: (name) => {
        const pageImport = pages[`./Pages/${name}.vue`];

        if (!pageImport) {
            throw new Error(
                `Component "${name}" not found. Please ensure the page exists in the 'Pages' folder.`
            );
        }

        return pageImport;
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .mount(el);
    },
});
