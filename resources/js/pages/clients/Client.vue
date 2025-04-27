<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps({
    client: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
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
        title: props.isEdit ? 'Edit Client' : 'Create Client',
    },
];

const form = useForm({
    name: props.client?.name || '',
    email: props.client?.email || '',
    phone: props.client?.phone || '',
    address: props.client?.address || '',
    status: props.client?.status || 'active'
});

const submit = () => {
    props.isEdit
        ? form.put(route('dashboard-clients-update', props.client.id))
        : form.post(route('dashboard-clients-store'));
};
</script>

<template>

    <Head :title="isEdit ? 'Edit Client' : 'Create Client'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">
                    {{ isEdit ? 'Edit Client' : 'Create New Client' }}
                </h1>
                <Link :href="route('dashboard-clients')" class="btn-secondary">
                Back to Clients
                </Link>
            </div>

            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Name -->
                        <div>
                            <label class="form-label">Full Name</label>
                            <Input v-model="form.name" type="text" required />
                            <p v-if="form.errors.name" class="form-error">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="form-label">Email</label>
                            <Input v-model="form.email" type="email" required />
                            <p v-if="form.errors.email" class="form-error">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="form-label">Phone</label>
                            <Input v-model="form.phone" type="tel" />
                            <p v-if="form.errors.phone" class="form-error">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="form-label">Status</label>
                            <Select class="w-full">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Status</SelectLabel>
                                        <SelectItem value="active">
                                            Active
                                        </SelectItem>
                                        <SelectItem value="inactive">
                                            Inactive
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label class="form-label">Address</label>
                            <Textarea v-model="form.address" rows="3"></Textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <Link :href="route('dashboard-clients')" class="btn-secondary">
                        Cancel
                        </Link>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Client' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
