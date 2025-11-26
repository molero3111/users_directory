<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';

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
    router.get('/dashboard', { page }, { preserveState: false, preserveScroll: true });
}

function onPageInputChange(event: Event) {
    const target = event.target as HTMLInputElement;
    let page = parseInt(target.value, 10);
    if (isNaN(page) || page < 1) page = 1;
    if (page > users.last_page) page = users.last_page;
    goToPage(page);
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
}
</style>

<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto py-8">
            <h1 class="text-2xl font-bold mb-6 justify-center flex">User Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="user in users.data" :key="user.id"
                    class="bg-white rounded-lg shadow p-6 flex flex-col items-start">
                    <div class="text-lg text-gray-600 font-semibold mb-2">{{ user.first_name }} {{ user.last_name }}
                    </div>
                    <div v-if="user.address" class="text-sm text-gray-600 mb-1">Country: <span class="font-medium">{{
                        user.address?.country
                            }}</span></div>
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
    </AppLayout>
</template>
