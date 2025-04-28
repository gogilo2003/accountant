<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { FileText, ArrowLeft, Save, Trash2 } from 'lucide-vue-next';
import ClientSelect from '@/components/ClientSelect.vue';
import DatePicker from '@/components/DatePicker.vue';
import { computed } from 'vue';

const props = defineProps({
    nextNumber: {
        type: Number,
        default: null
    },
    invoice: {
        type: Object,
        default: null
    },
    clients: {
        type: Array,
        default: () => []
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Invoices', href: route('dashboard-invoices') },
    { title: props.isEdit ? 'Edit Invoice' : 'Create Invoice', href: '' },
];

const form = useForm({
    client_id: props.invoice?.client_id || '',
    invoice_number: props.invoice?.invoice_number || props.nextNumber || '',
    issue_date: props.invoice?.issue_date || new Date().toISOString().split('T')[0],
    due_date: props.invoice?.due_date || '',
    items: props.invoice?.items || [
        { description: '', quantity: 1, unit_price: 0 }
    ],
    notes: props.invoice?.notes || '',
    status: props.invoice?.status || 'draft'
});

const addItem = () => {
    form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

const calculateTotal = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (item.quantity * item.unit_price);
    }, 0);
});

const submit = () => {
    form.transform(data => ({
        ...data,
        amount: calculateTotal.value
    }))[props.isEdit ? 'put' : 'post'](
        props.isEdit
            ? route('dashboard-invoices-update', props.invoice.id)
            : route('dashboard-invoices-store')
    );
};
</script>

<template>

    <Head :title="isEdit ? 'Edit Invoice' : 'Create Invoice'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <pre>{{ invoice }}</pre>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <FileText class="h-6 w-6" />
                    {{ isEdit ? 'Edit Invoice' : 'Create New Invoice' }}
                </h1>
                <Link :href="route('dashboard-invoices')" class="btn-secondary">
                <ArrowLeft class="h-5 w-5 mr-1" />
                Back to Invoices
                </Link>
            </div>

            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Client and Invoice Info -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <ClientSelect v-model="form.client_id" :clients="clients" :error="form.errors.client_id"
                            required />

                        <div>
                            <label class="form-label">Invoice Number</label>
                            <input v-model="form.invoice_number" type="text" class="form-input" required>
                            <p v-if="form.errors.invoice_number" class="form-error">
                                {{ form.errors.invoice_number }}
                            </p>
                        </div>

                        <DatePicker v-model="form.issue_date" label="Issue Date" required />

                        <DatePicker v-model="form.due_date" label="Due Date" required />
                    </div>

                    <!-- Invoice Items -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Invoice Items</h3>

                        <div class="space-y-4">
                            <div v-for="(item, index) in form.items" :key="index"
                                class="grid gap-4 md:grid-cols-12 items-end">
                                <div class="md:col-span-6">
                                    <label class="form-label">Description</label>
                                    <input v-model="item.description" type="text" class="form-input" required>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="form-label">Quantity</label>
                                    <input v-model.number="item.quantity" type="number" min="1" class="form-input"
                                        required>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="form-label">Unit Price</label>
                                    <input v-model.number="item.unit_price" type="number" min="0" step="0.01"
                                        class="form-input" required>
                                </div>

                                <div class="md:col-span-1">
                                    <button type="button" @click="removeItem(index)" class="btn-icon !text-red-500"
                                        title="Remove item">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <button type="button" @click="addItem" class="btn-secondary text-sm">
                                Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium">Subtotal:</span>
                                <span>${{ calculateTotal.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span>${{ calculateTotal.toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes and Status -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="form-label">Notes</label>
                            <textarea v-model="form.notes" rows="3" class="form-input"></textarea>
                        </div>

                        <div>
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-input">
                                <option value="draft">Draft</option>
                                <option value="sent">Sent</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <Link :href="route('dashboard-invoices')" class="btn-secondary">
                        Cancel
                        </Link>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            <Save class="h-5 w-5 mr-1" />
                            {{ form.processing ? 'Saving...' : 'Save Invoice' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
