<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Table from '@/components/Table.vue';
import Pagination from '@/components/Pagination.vue';
import SearchInput from '@/components/SearchInput.vue';
import { List, Pencil, Trash } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

const props = defineProps({
    clients: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
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
];

const search = ref(props.filters.search || '');

const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'phone', label: 'Phone' },
    { key: 'actions', label: 'Actions' }
];
</script>

<template>

    <Head title="Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with search and create button -->
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <h1 class="text-2xl font-bold">Client Management</h1>
                <div class="flex gap-3">
                    <SearchInput v-model="search" placeholder="Search clients..." />
                    <Link :href="route('dashboard-clients-create')" class="btn-primary">
                    Add New Client
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</h3>
                    <p class="text-2xl font-semibold">{{ clients.total }}</p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Clients</h3>
                    <p class="text-2xl font-semibold">{{clients.data.filter(c => c.status === 'active').length}}</p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">New This Month</h3>
                    <p class="text-2xl font-semibold">{{clients.data.filter(c => new Date(c.created_at) > new
                        Date(Date.now() - 30 * 24 * 60 * 60 * 1000)).length}}</p>
                </div>
            </div>

            <!-- Clients Table -->
            <div
                class="flex-1 rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <Table :columns="columns" :data="clients.data" :striped="true">
                    <template #cell-name="{ item }">
                        <td class="py-4 px-6">
                            <Link :href="route('dashboard-clients-show', item.id)"
                                class="font-medium text-primary hover:underline inline">
                            {{ item.name }}
                            </Link>
                        </td>
                    </template>
                    <template #cell-actions="{ item }">
                        <td class="px-6 py-4 flex gap-2">
                            <Link :href="route('dashboard-clients-edit', item.id)" class="btn-icon">
                            <Pencil class="h-4 w-4" />
                            </Link>
                            <Link :href="route('dashboard-clients-show', item.id)" class="btn-icon">
                            <List class="h-4 w-4" />
                            </Link>
                            <Link method="delete" :href="route('dashboard-clients-destroy', item.id)"
                                class="btn-icon cursor-pointer text-red-500">
                            <Trash class="h-4 w-4" />
                            </Link>
                        </td>
                    </template>
                </Table>
                <Pagination :links="clients.links" class="p-4" />
            </div>
        </div>
    </AppLayout>
</template>
