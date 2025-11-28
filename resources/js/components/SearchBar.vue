<template>
    <div class="mb-6">
        <div class="flex flex-col gap-2 w-full sm:flex-row sm:items-center">
            <div class="flex flex-col gap-2 w-full sm:flex-row sm:gap-2 sm:items-center">
                <input v-model="searchValue" type="text" placeholder="Search..."
                    class="border rounded px-3 py-2 flex-1 min-w-0" />
                <select v-model="selectedField" class="border rounded px-2 py-2">
                    <option v-for="field in fields" :key="field.value" :value="field.value">{{ field.label }}</option>
                </select>
                <button @click="addFilter" class="bg-blue-600 text-white px-3 py-2 rounded">Add Filter</button>
                <button @click="search" class="bg-green-600 text-white px-3 py-2 rounded">Search</button>
            </div>
        </div>
        <div v-if="filters.length" class="mt-4">
            <div class="font-semibold mb-2">Filters Applied:</div>
            <div class="flex flex-wrap gap-2">
                <span v-for="(filter, idx) in filters" :key="idx" class="px-3 py-1 rounded flex items-center gap-2">
                    <span class="font-medium">{{ filter.fieldLabel }}:</span> {{ filter.value }}
                    <button @click="removeFilter(idx)" class="text-red-600 font-bold ml-2">&times;</button>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';

const emit = defineEmits(['search']);

const searchValue = ref('');
const selectedField = ref('first_name');
const filters = ref<Array<{ field: string; fieldLabel: string; value: string }>>([]);

// Persist filters to localStorage
watch(filters, (newFilters) => {
    localStorage.setItem('user-filters', JSON.stringify(newFilters));
}, { deep: true });

// Load filters from localStorage on mount
onMounted(() => {
    const saved = localStorage.getItem('user-filters');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                filters.value = parsed;
            }
        } catch {}
    }
});

const fields = [
    { value: 'first_name', label: 'First Name' },
    { value: 'last_name', label: 'Last Name' },
    { value: 'email', label: 'Email' },
    { value: 'country', label: 'Country' },
    { value: 'city', label: 'City' },
    { value: 'post_code', label: 'Post Code' },
    { value: 'street', label: 'Street' }
];

function addFilter() {
    if (!searchValue.value.trim()) return;
    const fieldObj = fields.find(f => f.value === selectedField.value);
    if (!fieldObj) return;
    // Remove any existing filter of the same field
    const idx = filters.value.findIndex(f => f.field === selectedField.value);
    if (idx !== -1) {
        filters.value.splice(idx, 1);
    }
    filters.value.push({ field: selectedField.value, fieldLabel: fieldObj.label, value: searchValue.value });
    searchValue.value = '';
    selectedField.value = 'first_name';
}

function removeFilter(idx: number) {
    filters.value.splice(idx, 1);
}

function search() {
    // Emit filters to parent
    emit('search', filters.value);
}
</script>

<style scoped>
input,
select {
    outline: none;
}
select {
    background-color: #111;
    color: #fff;
}
option {
    background-color: #111;
    color: #fff;
}
</style>
