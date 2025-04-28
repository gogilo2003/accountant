<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { FileText, Pencil, Plus, Search } from 'lucide-vue-next';
import Table from '@/components/Table.vue';
import Pagination from '@/components/Pagination.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import SearchInput from '@/components/SearchInput.vue';
import { ref } from 'vue';

const props = defineProps({
    invoices: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Invoices', href: route('dashboard-invoices') },
];

const search = ref(props.filters.search || '');

const columns = [
    { key: 'invoice_number', label: 'Invoice #', sortable: true },
    { key: 'client', label: 'Client', sortable: true },
    { key: 'issue_date', label: 'Date', sortable: true },
    { key: 'amount', label: 'Amount', sortable: true, align: 'right' },
    { key: 'status', label: 'Status', sortable: true, align: 'center' },
    { key: 'actions', label: '', align: 'right' }
];
</script>

<template>

    <Head title="Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header -->
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <h1 class="text-2xl font-bold">Invoice Management</h1>
                <div class="flex gap-3">
                    <SearchInput v-model="search" />
                    <Link :href="route('dashboard-invoices-create')" class="btn-primary">
                    <Plus class="h-5 w-5 mr-1" />
                    New Invoice
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Invoices</h3>
                    <p class="text-2xl font-semibold">{{ invoices.total }}</p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid</h3>
                    <p class="text-2xl font-semibold">{{invoices.data.filter(i => i.status === 'paid').length}}</p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</h3>
                    <p class="text-2xl font-semibold">{{invoices.data.filter(i => i.status === 'sent').length}}</p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue</h3>
                    <p class="text-2xl font-semibold">{{invoices.data.filter(i => i.status === 'sent' && new
                        Date(i.due_date) < new Date()).length}}</p>
                </div>
            </div>

            <!-- Invoices Table -->
            <div
                class="flex-1 rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <Table :columns="columns" :data="invoices.data" hover-effect striped>
                    <template #cell-invoice_number="{ item }">
                        <td class="px-6 py-4">
                            <Link :href="route('dashboard-invoices-show', item.id)"
                                class="font-medium text-primary hover:underline flex items-center gap-1">
                            <FileText class="h-4 w-4" />
                            {{ item.invoice_number }}
                            </Link>
                        </td>
                    </template>

                    <template #cell-issue_date="{ item }">
                        <td class="px-6 py-4">
                            {{ new Date(item.issue_date).toLocaleDateString() }}
                        </td>
                    </template>

                    <template #cell-client="{ item }">
                        <td class="px-6 py-4">
                            {{ item.client.name }}
                        </td>
                    </template>

                    <template #cell-amount="{ item }">
                        <td class="px-6 py-4">
                            Ksh {{ item.amount.toLocaleString() }}
                        </td>
                    </template>

                    <template #cell-status="{ item }">
                        <td class="px-6 py-4">
                            <StatusBadge :status="item.status" />
                        </td>
                    </template>

                    <template #cell-actions="{ item }">
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <Link :href="route('dashboard-invoices-edit', item.id)" class="btn-icon" title="Edit">
                                <Pencil class="h-4 w-4" />
                                </Link>
                            </div>
                        </td>
                    </template>
                </Table>
                <Pagination :links="invoices.links" class="p-4" />
            </div>
        </div>
    </AppLayout>
</template>
