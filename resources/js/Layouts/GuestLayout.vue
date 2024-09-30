<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

// Function to toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};
</script>

<template>
  <div>
    <header class="bg-blue-800 text-white p-3 flex items-center justify-between shadow">
      <div class="flex items-center">
        <img src="/resources/css/mdc.png" alt="Logo" class="mx-auto w-14 h-14 rounded-full ml-4" />
        <nav>
          <div class="flex ml-16 gap-10">
            <a href="/dashboard/guest">Dashboard</a>
            <a href="/calendar">Calendar</a>
          </div>
        </nav>
      </div>

      <!-- Dropdown for login -->
      <div class="relative inline-block text-left mr-10">
        <!-- Dropdown Button -->
        <button @click="toggleDropdown" class="inline-flex justify-center w-full rounded-md border border-gray-500 shadow-sm px-7 py-0.5 bg-white text-xs text-black hover:bg-gray-50 focus:outline-none">
          Login
          <svg v-if="!isOpen" class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          <svg v-if="isOpen" id="icon" class="-mr-1 ml-2 h-5 w-5 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="isOpen" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
          <div class="py-1">
            <a href="../Pages/Auth/Login.vue" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">Login</a>
 
          </div>
        </div>
      </div>
    </header>

    <main class="bg-gray-200 min-h-screen">
      <slot />
    </main>
  </div>
</template>
