<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { FileText, Printer, Mail, ArrowLeft, Download } from 'lucide-vue-next';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    invoice: {
        type: Object,
        required: true
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Invoices', href: route('dashboard-invoices') },
    { title: props.invoice.invoice_number },
];

const printInvoice = () => {
    window.print();
};

const sendInvoice = () => {
    // Implement email sending logic
};
</script>

<template>

    <Head :title="`Invoice ${invoice.invoice_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with actions -->
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <FileText class="h-6 w-6" />
                        Invoice #{{ invoice.invoice_number }}
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <StatusBadge :status="invoice.status" />
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Issued: {{ new Date(invoice.issue_date).toLocaleDateString() }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button @click="printInvoice" class="btn-secondary">
                        <Printer class="h-5 w-5 mr-1" />
                        Print
                    </button>
                    <button @click="sendInvoice" class="btn-secondary">
                        <Mail class="h-5 w-5 mr-1" />
                        Send
                    </button>
                    <Link :href="route('dashboard-invoices-download', invoice.id)" class="btn-primary">
                    <Download class="h-5 w-5 mr-1" />
                    Download PDF
                    </Link>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Client Info -->
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800 md:col-span-1">
                    <h3 class="text-lg font-semibold mb-4">Bill To</h3>
                    <div class="space-y-2">
                        <p class="font-medium">{{ invoice.client.name }}</p>
                        <p v-if="invoice.client.email">{{ invoice.client.email }}</p>
                        <p v-if="invoice.client.phone">{{ invoice.client.phone }}</p>
                        <p v-if="invoice.client.address" class="whitespace-pre-line">{{ invoice.client.address }}</p>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800 md:col-span-2">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Invoice Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Invoice Number:</span>
                                    <span>{{ invoice.invoice_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Issue Date:</span>
                                    <span>{{ new Date(invoice.issue_date).toLocaleDateString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Due Date:</span>
                                    <span>{{ new Date(invoice.due_date).toLocaleDateString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                    <StatusBadge :status="invoice.status" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Payment Info</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                                    <span>${{ invoice.amount.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Tax:</span>
                                    <span>$0.00</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span>${{ invoice.amount.toLocaleString() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div
                class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(item, index) in invoice.items" :key="index">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ item.description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ item.quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                ${{ item.unit_price.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                ${{ (item.quantity * item.unit_price).toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes and Footer -->
            <div class="grid gap-4 md:grid-cols-2">
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-2">Notes</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                        {{ invoice.notes || 'No notes provided' }}
                    </p>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-2">Payment Instructions</h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Please make payment to:<br>
                        Account Name: Your Company Name<br>
                        Bank: Example Bank<br>
                        Account #: 12345678<br>
                        Routing #: 987654321
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
