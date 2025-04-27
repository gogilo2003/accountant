<script setup lang="ts">
import { Search } from 'lucide-vue-next';

const model = defineModel<string>();
</script>

<template>
    <div class="relative rounded-md shadow-sm">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <Search class="h-5 w-5 text-gray-400" />
        </div>
        <input v-model="model" type="text"
            class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 bg-white dark:bg-gray-800"
            placeholder="Search..." />
    </div>
</template>
