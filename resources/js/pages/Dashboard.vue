<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto py-8">
            <h1 class="text-2xl font-bold mb-2 justify-center flex">User Dashboard</h1>
            <SearchBar @search="handleSearch" />
            <div class="flex justify-end mb-4">
                <button @click="openCreateModal" class="px-4 py-2 rounded bg-gray-400 text-black font-semibold">Create User</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="user in users.data" :key="user.id" :class="[
                    'bg-white rounded-lg shadow p-6 flex flex-col items-start relative transition-all duration-500 fade-in',
                    updatedUserId === user.id ? 'card-updated' : '',
                    deletedUserId === user.id ? 'card-deleted' : ''
                ]">
                    <div class="flex items-center w-full justify-between mb-2">
                        <div class="text-lg text-gray-600 font-semibold">{{ user.first_name }} {{ user.last_name }}
                        </div>
                        <button @click="openModal(user)" class="ml-2 text-gray-500 hover:text-blue-600" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                            </svg>
                        </button>
                    </div>
                    <div v-if="user.address?.country" class="text-sm text-gray-600 mb-1">Country: <span
                            class="font-medium">{{
                                user.address?.country }}</span></div>
                </div>
            </div>
            <div class="mt-6 flex justify-center">
                <button v-if="users.prev_page_url" @click="goToPage(users.current_page - 1)"
                    class="px-4 py-2 mr-2 rounded bg-gray-400 text-black font-semibold">Previous</button>
                <span class="px-4 py-2 flex items-center gap-2">
                    Page
                    <input type="number" min="1" :max="users.last_page" :value="users.current_page"
                        @change="onPageInputChange($event)" :style="`width: ${String(users.current_page).length + 3}ch`"
                        class="px-2 py-1 border rounded text-center" />
                    of {{ users.last_page }}
                </span>
                <button v-if="users.next_page_url" @click="goToPage(users.current_page + 1)"
                    class="px-4 py-2 ml-2 rounded bg-gray-400 text-black font-semibold">Next</button>
            </div>
        </div>
        <UserModal :open="modalOpen" :user="selectedUser" :mode="modalMode" @close="closeModal" @updated="handleUserUpdated"
            @deleted="handleUserDeleted" @created="handleUserCreated" />
    </AppLayout>
</template>
<script setup lang="ts">
import SearchBar from '@/components/SearchBar.vue';
import UserModal from '@/components/UserModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
const activeFilters = ref<Array<{ field: string; value: string }>>([]);

const modalOpen = ref(false);
const selectedUser = ref<any>(null);
const modalMode = ref<'create' | 'update'>('update');

function handleSearch(filters: Array<{ field: string; fieldLabel: string; value: string }>) {
    activeFilters.value = filters.map(f => ({ field: f.field, value: f.value }));
    // Build query params
    const params: Record<string, string | number> = { page: 1 };
    for (const filter of activeFilters.value) {
        params[filter.field] = filter.value;
    }
    router.get('/dashboard', params, { preserveState: false, preserveScroll: true });
}

function openModal(user: any) {
    selectedUser.value = user;
    modalMode.value = 'update';
    modalOpen.value = true;
}

function openCreateModal() {
    selectedUser.value = null;
    modalMode.value = 'create';
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    selectedUser.value = null;
}

interface Address {
    country: string;
    city: string;
    post_code: string;
    street: string;
}

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    address?: Address;
}

interface UsersPagination {
    data: User[];
    current_page: number;
    last_page: number;
    prev_page_url?: string;
    next_page_url?: string;
}

const users = usePage().props.users as UsersPagination;
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

function goToPage(page: number) {
    const params: Record<string, string | number> = { page };
    let filters = [];
    try {
        const saved = localStorage.getItem('user-filters');
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                filters = parsed;
            }
        }
    } catch {}
    for (const filter of filters) {
        params[filter.field] = filter.value;
    }
    router.get('/dashboard', params, { preserveState: false, preserveScroll: true });
}

function onPageInputChange(event: Event) {
    const target = event.target as HTMLInputElement;
    let page = parseInt(target.value, 10);
    if (isNaN(page) || page < 1) page = 1;
    if (page > users.last_page) page = users.last_page;
    goToPage(page);
}

const updatedUserId = ref<number | null>(null);
function handleUserUpdated(updatedUser: any) {
    const idx = users.data.findIndex(u => u.id === updatedUser.id);
    if (idx !== -1) {
        users.data[idx] = { ...users.data[idx], ...updatedUser };
        updatedUserId.value = updatedUser.id;
        setTimeout(() => {
            updatedUserId.value = null;
        }, 400);
    }
    closeModal();
}

function handleUserCreated() {
    // Clear filters and go to page 1 so new user appears
    activeFilters.value = [];
    router.get('/dashboard', { page: 1 }, { preserveState: false, preserveScroll: true });
    closeModal();
}

const deletedUserId = ref<number | null>(null);

function handleUserDeleted(userId: number) {
    deletedUserId.value = userId;
    setTimeout(() => {
        const idx = users.data.findIndex(u => u.id === userId);
        if (idx !== -1) {
            users.data.splice(idx, 1);
        }
        deletedUserId.value = null;
    }, 400); // Animation duration
    closeModal();
}
</script>

<style scoped>
.container {
    max-width: 900px;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}

.card-updated {
    background-color: #242d35;
    transition: background-color 0.4s;
}

.card-deleted {
    opacity: 0;
    transition: opacity 0.4s;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px);}
  to { opacity: 1; transform: translateY(0);}
}
.fade-in {
  animation: fade-in 1s ease;
}
</style>