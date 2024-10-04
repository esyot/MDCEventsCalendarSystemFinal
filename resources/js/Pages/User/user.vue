<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';

defineOptions({ layout: MainLayout});

const props = defineProps({
  users: {
    type: Array,
    required: true,
  },
});
</script>

<template>
   
   <div class="overflow-x-auto mx-4 mt-4">
  <table class="min-w-full bg-white border border-gray-300">
    <thead>
      <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
        <th class="py-3 px-6 text-left">First Name</th>
        <th class="py-3 px-6 text-left">Last Name</th>
        <th class="py-3 px-6 text-left">Role</th>
      </tr>
    </thead>
    <tbody class="text-gray-600 text-sm font-light">
      <tr v-for="user in users" :key="user.id" class="hover:bg-gray-200">
        <td class="py-3 px-6">{{ user.fname }}</td>
        <td class="py-3 px-6">{{ user.lname }}</td>
        <td class="py-3 px-6">{{ user.role_name }}</td>
      </tr>
    </tbody>
  </table>
</div>
</template>



