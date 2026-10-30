import { h } from 'vue';
import DefaultTheme from 'vitepress/theme-without-fonts';
import DemoLink from './DemoLink.vue';
import './custom.css';

export default {
    extends: DefaultTheme,
    Layout() {
        return h(DefaultTheme.Layout, null, {
            'doc-before': () => h(DemoLink)
        });
    }
};
