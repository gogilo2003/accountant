<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    required: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedDate = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <div>
        <label class="form-label">{{ label }}</label>
        <input v-model="selectedDate" type="date" class="form-input" :required="required">
    </div>
</template>
