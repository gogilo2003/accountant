<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true
    },
    clients: {
        type: Array as () => Array<{ id: string | number; name: string }>,
        required: true
    },
    error: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <div>
        <label class="form-label">Client</label>
        <select v-model="selectedValue" class="form-input" required>
            <option value="" disabled>Select a client</option>
            <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.name }}
            </option>
        </select>
        <p v-if="error" class="form-error">
            {{ error }}
        </p>
    </div>
</template>
