<script setup>
import { computed } from 'vue';
import { useData } from 'vitepress';

const { frontmatter } = useData();

// Every reference page has a Blade view in the demo app, which lists the example files it includes
const sourceUrl = computed(() => {
    const path = String(frontmatter.value.demo || '').replace(/^\/reference\//, '');

    return `https://github.com/larajax/amber/blob/main/resources/views/reference/${path}.blade.php`;
});
</script>

<template>
    <div v-if="frontmatter.demo" class="custom-block tip demo-link">
        <p class="custom-block-title">
            Run it in the demo app
        </p>
        <p>
            Every example on this page is live in Amber at <code>{{ frontmatter.demo }}</code>, where you can
            change it and save it. <a href="/getting-started#run-the-demo-app">Spin up the demo app</a>, or
            <a :href="sourceUrl" target="_blank" rel="noopener">browse the page's source</a>.
        </p>
    </div>
</template>
