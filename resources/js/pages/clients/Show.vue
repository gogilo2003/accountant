<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import StatCard from '../../components/StatCard.vue';

const props = defineProps({
    client: {
        type: Object,
        required: true
    },
    financialSummary: {
        type: Object,
        required: true
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Clients',
        href: route('dashboard-clients'),
    },
    {
        title: props.client.name,
    },
];
</script>

<template>

    <Head :title="client.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with actions -->
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold">{{ client.name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Client since {{ new Date(client.created_at).toLocaleDateString() }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('dashboard-clients-edit', client.id)" class="btn-secondary">
                    Edit Client
                    </Link>
                </div>
            </div>

            <!-- Client Stats -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <StatCard title="Total Value" :value="financialSummary.total_value" icon="dollar" />
                <StatCard title="Open Invoices" :value="financialSummary.open_invoices" icon="file-text" />
                <StatCard title="Transactions" :value="financialSummary.transaction_count" icon="credit-card" />
            </div>

            <!-- Client Details -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Basic Info -->
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-semibold">Client Information</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="font-medium">{{ client.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="font-medium">{{ client.phone || 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                            <p class="font-medium" v-if="client.address">{{ client.address }}</p>
                            <p class="text-gray-500 dark:text-gray-400" v-else>Not provided</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                :class="{
                                    'bg-green-100 text-green-800': client.status === 'active',
                                    'bg-red-100 text-red-800': client.status === 'inactive'
                                }">
                                {{ client.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-semibold">Recent Activity</h3>
                    <div class="space-y-4">
                        <div v-for="activity in financialSummary.recent_activity" :key="activity.id">
                            <p class="text-sm font-medium">{{ activity.description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ new Date(activity.date).toLocaleString() }}
                            </p>
                        </div>
                        <Link v-if="financialSummary.recent_activity.length > 0" :href="route('dashboard-transactions')"
                            class="inline-block text-sm font-medium text-primary hover:underline">
                        View all activity
                        </Link>
                        <p v-else class="text-sm text-gray-500 dark:text-gray-400">
                            No recent activity
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
