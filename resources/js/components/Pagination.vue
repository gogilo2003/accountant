<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links: {
        type: Array as () => Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>,
        required: true
    }
});

const getClass = (link: { active: boolean }) => {
    return link.active
        ? 'relative z-10 inline-flex items-center bg-primary-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
        : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0';
};
</script>

<template>
    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 sm:px-0">
        <div class="-mt-px flex w-0 flex-1">
            <Link v-if="links[0].url" :href="links[0].url"
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-200">
            Previous
            </Link>
        </div>

        <div class="hidden md:-mt-px md:flex">
            <Link v-for="(link, index) in links.slice(1, -1)" :key="index" :href="link.url || '#'"
                :class="getClass(link)" v-html="link.label" />
        </div>

        <div class="-mt-px flex w-0 flex-1 justify-end">
            <Link v-if="links[links.length - 1].url" :href="links[links.length - 1].url"
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-200">
            Next
            </Link>
        </div>
    </nav>
</template>
