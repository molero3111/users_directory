<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center modal-background">
            <div class="bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-md relative animate-modal">
                <button @click="close" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
                <h2 class="text-xl font-bold mb-4">User Details</h2>
                <form @submit.prevent="save">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">First Name</label>
                        <input v-model="form.first_name" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Last Name</label>
                        <input v-model="form.last_name" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Country</label>
                        <input v-model="form.address.country" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">City</label>
                        <input v-model="form.address.city" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Post Code</label>
                        <input v-model="form.address.post_code" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Street</label>
                        <input v-model="form.address.street" type="text" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="flex flex-row justify-center gap-2 mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                        <button type="button" @click="deleteUser"
                            class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                    </div>
                    <div v-if="confirmDelete" class="mt-4 w-full flex flex-col items-center">
                        <p class="mb-2 text-sm font-bold text-red-700 text-center">Are you sure you want to delete this
                            user?</p>
                        <div class="flex gap-2 justify-center">
                            <button @click="confirmDeleteUser"
                                class="bg-red-600 text-white px-3 py-1 rounded">Yes</button>
                            <button @click="cancelDelete" class="bg-gray-400 text-black px-3 py-1 rounded">No</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { ref, watch, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    open: boolean;
    user: any;
}>();
const emit = defineEmits(['close', 'updated', 'deleted']);

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    address: {
        country: '',
        city: '',
        post_code: '',
        street: '',
    },
});

watch(() => props.open, async (open) => {
    if (open && props.user) {
        // Fetch user data from show endpoint
        const response = await fetch(`/users/${props.user.id}`);
        if (response.ok) {
            const user = await response.json();
            form.first_name = user.first_name;
            form.last_name = user.last_name;
            form.email = user.email;
            form.address = { ...user.address };
        }
    }
});

function close() {
    emit('close');
}

function save() {
    router.patch(`/users/${props.user.id}`, form, {
        onSuccess: async (page) => {
            // Get updated user data from backend
            const response = await fetch(`/users/${props.user.id}`);
            if (response.ok) {
                const user = await response.json();
                emit('updated', user); // Emit updated user data
            }
        },
    });
}

const confirmDelete = ref(false);

function deleteUser() {
    confirmDelete.value = true;
}

function confirmDeleteUser() {
    router.delete(`/users/${props.user.id}`, {
        onSuccess: () => emit('deleted', props.user.id),
    });
    confirmDelete.value = false;
}

function cancelDelete() {
    confirmDelete.value = false;
}

</script>
<style scoped>
@keyframes modal-fade {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-modal {
    animation: modal-fade 0.3s ease;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.modal-background {
    background: rgba(0, 0, 0, 0.4);
}
</style>
