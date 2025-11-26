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
</script>

<style scoped>
.container {
    max-width: 900px;
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
                    class="px-4 py-2 mr-2 rounded bg-gray-200">Previous</button>
                <span class="px-4 py-2">Page {{ users.current_page }} of {{ users.last_page }}</span>
                <button v-if="users.next_page_url" @click="goToPage(users.current_page + 1)"
                    class="px-4 py-2 ml-2 rounded bg-gray-400 text-black font-semibold">Next</button>
            </div>
        </div>
    </AppLayout>
</template>
