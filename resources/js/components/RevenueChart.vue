<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
import { useDark } from '@vueuse/core';

Chart.register(...registerables);

const props = defineProps({
    data: {
        type: Array as () => Array<{
            month: string;
            revenue: number;
            expenses: number;
        }>,
        default: () => []
    }
});

const isDark = useDark();
const chartRef = ref<HTMLCanvasElement>();
const chartInstance = ref<Chart>();

const initChart = () => {
    if (!chartRef.value) return;

    // Destroy previous instance if exists
    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    const ctx = chartRef.value.getContext('2d');
    if (!ctx) return;

    const labels = props.data.map(item => item.month);
    const revenueData = props.data.map(item => item.revenue);
    const expensesData = props.data.map(item => item.expenses);

    chartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenueData,
                    backgroundColor: '#4f46e5', // Primary color
                    borderRadius: 6,
                    barPercentage: 0.7
                },
                {
                    label: 'Expenses',
                    data: expensesData,
                    backgroundColor: '#ef4444', // Red color
                    borderRadius: 6,
                    barPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: isDark.value ? '#e5e7eb' : '#374151',
                        font: {
                            family: 'Inter, sans-serif'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: isDark.value ? '#1f2937' : '#ffffff',
                    titleColor: isDark.value ? '#e5e7eb' : '#111827',
                    bodyColor: isDark.value ? '#e5e7eb' : '#111827',
                    borderColor: isDark.value ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: (context) => {
                            return ` ${context.dataset.label}: $${context.raw?.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: isDark.value ? '#9ca3af' : '#6b7280'
                    }
                },
                y: {
                    grid: {
                        color: isDark.value ? '#374151' : '#e5e7eb',
                        drawBorder: false
                    },
                    ticks: {
                        color: isDark.value ? '#9ca3af' : '#6b7280',
                        callback: (value) => {
                            return `$${Number(value).toLocaleString()}`;
                        }
                    }
                }
            }
        }
    });
};

// Watch for data changes
watch(() => props.data, () => {
    initChart();
}, { deep: true });

// Watch for dark mode changes
watch(isDark, () => {
    initChart();
});

onMounted(() => {
    initChart();
});
</script>

<template>
    <div class="relative h-80 w-full">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
