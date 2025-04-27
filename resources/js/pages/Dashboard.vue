<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import StatCard from '../components/StatCard.vue';
import RecentTransactions from '../components/RecentTransactions.vue';
import RecentInvoices from '../components/RecentInvoices.vue';
import RevenueChart from '../components/RevenueChart.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
        default: () => ({
            revenue: 0,
            outstanding: 0,
            clients: 0,
            transactions: 0
        })
    },
    recentTransactions: {
        type: Array,
        default: () => []
    },
    recentInvoices: {
        type: Array,
        default: () => []
    },
    chartData: {
        type: Array,
        default: () => []
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    }
];
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Stats Overview -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <StatCard unit="Ksh" title="Total Revenue" :value="stats.revenue" icon="dollar" trend="up"
                    :percentage="12.5" link="dashboard-reports-profit-loss" />
                <StatCard unit="Ksh" title="Outstanding" :value="stats.outstanding" icon="alert-circle" trend="down"
                    :percentage="8.2" link="dashboard-invoices" />
                <StatCard title="Active Clients" :value="stats.clients" icon="users" trend="up" :percentage="5.7"
                    link="dashboard-clients" />
                <StatCard title="Transactions" :value="stats.transactions" icon="credit-card" trend="up"
                    :percentage="18.3" link="dashboard-transactions" />
            </div>

            <!-- Revenue Chart -->
            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <RevenueChart :data="chartData" />
            </div>

            <!-- Recent Activity -->
            <div class="grid gap-4 md:grid-cols-2">
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-semibold">Recent Transactions</h3>
                    <RecentTransactions :transactions="recentTransactions" />
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-semibold">Recent Invoices</h3>
                    <RecentInvoices :invoices="recentInvoices" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
