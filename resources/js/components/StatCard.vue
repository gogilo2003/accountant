<script setup lang="ts">
import { ArrowUpRight, ArrowDownRight, AlertCircle, DollarSign, Users, CreditCard } from 'lucide-vue-next';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    icon: {
        type: String,
        default: 'dollar'
    },
    trend: {
        type: String,
        validator: (value: string) => ['up', 'down', 'neutral'].includes(value),
        default: 'neutral'
    },
    percentage: {
        type: Number,
        default: 0
    },
    link: {
        type: String,
        default: ''
    }
});

const icons = {
    dollar: DollarSign,
    alert: AlertCircle,
    users: Users,
    card: CreditCard
};

const iconComponent = icons[props.icon] || DollarSign;
</script>

<template>
    <div
        class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <component :is="iconComponent" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</span>
            </div>
            <span class="flex items-center text-xs font-medium" :class="{
                'text-green-500': trend === 'up',
                'text-red-500': trend === 'down',
                'text-gray-500': trend === 'neutral'
            }">
                <component :is="trend === 'up' ? ArrowUpRight : ArrowDownRight" class="h-3 w-3 mr-1" />
                {{ percentage }}%
            </span>
        </div>

        <div class="mt-2">
            <p class="text-2xl font-semibold">
                {{ typeof value === 'number' ? `$${value.toLocaleString()}` : value }}
            </p>
        </div>

        <a v-if="link" :href="route(link)" class="mt-2 inline-block text-xs font-medium text-primary hover:underline">
            View all
        </a>
    </div>
</template>
