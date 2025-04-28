<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value: string) => ['draft', 'sent', 'paid', 'cancelled'].includes(value)
    }
});

const badgeClasses = computed(() => {
    return {
        'draft': 'bg-gray-100 text-gray-800',
        'sent': 'bg-blue-100 text-blue-800',
        'paid': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    }[props.status];
});
</script>

<template>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="badgeClasses">
        {{ status }}
    </span>
</template>
