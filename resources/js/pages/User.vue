
<template>
  <div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">User Dashboard</h1>
    <table class="min-w-full  border">
      <thead>
        <tr>
          <th class="py-2 px-4 border">First Name</th>
          <th class="py-2 px-4 border">Last Name</th>
          <th class="py-2 px-4 border">Email</th>
          <th class="py-2 px-4 border">Country</th>
          <th class="py-2 px-4 border">City</th>
          <th class="py-2 px-4 border">Post Code</th>
          <th class="py-2 px-4 border">Street</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id">
          <td class="py-2 px-4 border">{{ user.id }}</td>
          <td class="py-2 px-4 border">{{ user.first_name }}</td>
          <td class="py-2 px-4 border">{{ user.last_name }}</td>
          <td class="py-2 px-4 border">{{ user.email }}</td>
          <td class="py-2 px-4 border">{{ user.address?.country }}</td>
          <td class="py-2 px-4 border">{{ user.address?.city }}</td>
          <td class="py-2 px-4 border">{{ user.address?.post_code }}</td>
          <td class="py-2 px-4 border">{{ user.address?.street }}</td>
        </tr>
      </tbody>
    </table>
    <div class="mt-6 flex justify-center">
      <button
        v-if="users.prev_page_url"
        @click="goToPage(users.current_page - 1)"
        class="px-4 py-2 mr-2 rounded"
      >Previous</button>
      <span class="px-4 py-2">Page {{ users.current_page }} of {{ users.last_page }}</span>
      <button
        v-if="users.next_page_url"
        @click="goToPage(users.current_page + 1)"
        class="px-4 py-2 ml-2 rounded"
      >Next</button>
    </div>
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
const users = usePage().props.users;

function goToPage(page) {
  router.get('/users', { page }, { preserveState: true, preserveScroll: true });
}
</script>

<style scoped>
.container {
  max-width: 900px;
}
</style>
