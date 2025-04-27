<script setup lang="ts">
import { FileText, CheckCircle, Clock, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    invoices: {
        type: Array,
        default: () => []
    }
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
};

const formatAmount = (amount: number) => {
    return amount.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD'
    });
};

const statusIcons = {
    paid: { icon: CheckCircle, color: 'text-green-500' },
    sent: { icon: Clock, color: 'text-yellow-500' },
    draft: { icon: FileText, color: 'text-gray-500' },
    overdue: { icon: AlertCircle, color: 'text-red-500' }
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="invoice in invoices" :key="invoice.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-gray-100 p-2 dark:bg-gray-700">
                    <component :is="statusIcons[invoice.status].icon" class="h-4 w-4"
                        :class="statusIcons[invoice.status].color" />
                </div>
                <div>
                    <p class="text-sm font-medium">Invoice #{{ invoice.invoice_number }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDate(invoice.issue_date) }} • {{ invoice.client?.name }}
                    </p>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium">
                    {{ formatAmount(invoice.amount) }}
                </p>
            </div>
        </div>

        <a v-if="invoices.length > 0" :href="route('dashboard-invoices')"
            class="inline-block w-full pt-2 text-center text-sm font-medium text-primary hover:underline">
            View all invoices
        </a>
        <p v-else class="text-sm text-gray-500 dark:text-gray-400">
            No recent invoices
        </p>
    </div>
</template>
