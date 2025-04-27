<script setup lang="ts">
import { CreditCard, ArrowUpRight, ArrowDownRight } from 'lucide-vue-next';

const props = defineProps({
    transactions: {
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
</script>

<template>
    <div class="space-y-4">
        <div v-for="transaction in transactions" :key="transaction.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-gray-100 p-2 dark:bg-gray-700">
                    <CreditCard class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-sm font-medium">{{ transaction.description }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDate(transaction.transaction_date) }}
                    </p>
                </div>
            </div>
            <div class="flex items-center">
                <component :is="transaction.direction === 'credit' ? ArrowUpRight : ArrowDownRight" class="h-4 w-4 mr-1"
                    :class="{
                        'text-green-500': transaction.direction === 'credit',
                        'text-red-500': transaction.direction === 'debit'
                    }" />
                <span class="text-sm font-medium">
                    {{ formatAmount(transaction.amount) }}
                </span>
            </div>
        </div>

        <a v-if="transactions.length > 0" :href="route('dashboard-transactions')"
            class="inline-block w-full pt-2 text-center text-sm font-medium text-primary hover:underline">
            View all transactions
        </a>
        <p v-else class="text-sm text-gray-500 dark:text-gray-400">
            No recent transactions
        </p>
    </div>
</template>
