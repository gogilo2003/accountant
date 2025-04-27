<script setup lang="ts">
import { ChevronUp, ChevronDown } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    align?: 'left' | 'center' | 'right';
    width?: string;
}

const props = defineProps({
    columns: {
        type: Array as () => Column[],
        required: true,
        validator: (cols: Column[]) => cols.length > 0
    },
    data: {
        type: Array as () => Record<string, any>[],
        required: true
    },
    hoverEffect: {
        type: Boolean,
        default: true
    },
    striped: {
        type: Boolean,
        default: false
    },
    compact: {
        type: Boolean,
        default: false
    }
});

const sortColumn = ref('');
const sortDirection = ref<'asc' | 'desc'>('asc');

const sortData = (column: string) => {
    if (!props.columns.find(c => c.key === column)?.sortable) return;

    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
};

const sortedData = computed(() => {
    if (!sortColumn.value) return props.data;

    return [...props.data].sort((a, b) => {
        const aValue = a[sortColumn.value];
        const bValue = b[sortColumn.value];

        // Handle null/undefined values
        if (aValue == null) return sortDirection.value === 'asc' ? -1 : 1;
        if (bValue == null) return sortDirection.value === 'asc' ? 1 : -1;

        // Numeric comparison
        if (typeof aValue === 'number' && typeof bValue === 'number') {
            return sortDirection.value === 'asc' ? aValue - bValue : bValue - aValue;
        }

        // String comparison
        const aString = String(aValue);
        const bString = String(bValue);

        return sortDirection.value === 'asc'
            ? aString.localeCompare(bString)
            : bString.localeCompare(aString);
    });
});

const getAlignment = (column: Column) => {
    switch (column.align) {
        case 'center': return 'text-center';
        case 'right': return 'text-right';
        default: return 'text-left';
    }
};

const rowClasses = computed(() => {
    return [
        props.hoverEffect && 'hover:bg-gray-50 dark:hover:bg-gray-700',
        props.striped && 'even:bg-gray-50 dark:even:bg-gray-800/50',
        props.compact ? 'py-2' : 'py-4'
    ].filter(Boolean).join(' ');
});
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th v-for="column in columns" :key="column.key" scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider" :class="[
                            getAlignment(column),
                            column.sortable ? 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700' : '',
                            compact ? 'py-2' : 'py-3'
                        ]" :style="column.width ? { width: column.width } : {}" @click="sortData(column.key)">
                        <div class="flex items-center justify-between">
                            <span class="whitespace-nowrap">
                                {{ column.label }}
                            </span>
                            <span v-if="column.sortable && sortColumn === column.key" class="ml-2">
                                <component :is="sortDirection === 'asc' ? ChevronUp : ChevronDown" class="h-4 w-4" />
                            </span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="(item, index) in sortedData" :key="index" :class="rowClasses">
                    <slot v-for="column in columns" :name="`cell-${column.key}`" :item="item" :index="index">
                        <td class="px-6 text-sm" :class="[
                            getAlignment(column),
                            compact ? 'py-2' : 'py-4'
                        ]">
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ item[column.key] }}
                            </span>
                        </td>
                    </slot>
                </tr>
                <tr v-if="data.length === 0">
                    <td :colspan="columns.length"
                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No records found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
