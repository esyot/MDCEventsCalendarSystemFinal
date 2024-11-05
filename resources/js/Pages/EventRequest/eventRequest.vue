<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });
</script>

<template>
    <div class="container mx-auto p-4">
      <!-- <h1 class="text-2xl font-bold mb-4">Event Request</h1> -->
      <div class="bg-gray-200 p-4 rounded">
        <table class="min-w-full border">
          <thead>
            <tr class="bg-gray-300">
              <th class="px-4 py-2 text-left">Event</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(event, index) in events" :key="index" class="bg-white border-b">
              <td class="px-4 py-2">{{ event.name }}</td>
              <td class="px-4 py-2">{{ event.status }}</td>
              <td class="px-4 py-2 flex space-x-2">
                <button @click="viewEvent(event.id)" class="text-blue-500 hover:text-blue-700">
                  <i class="fas fa-eye"></i>
                </button>
                <button @click="editEvent(event.id)" class="text-yellow-500 hover:text-yellow-700">
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  @click="deleteEvent(event.id)" 
                  class="text-red-500 hover:text-red-700">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  
  <style scoped>
  .container {
    max-width: 900px;
  }
  </style>
  