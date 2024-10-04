<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

// Function to toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};
</script>

<template>
  <div class="">
    <header class="bg-blue-800 text-white p-2 flex items-center justify-between shadow z-50">
      <div class="flex items-center">
        <img src="/resources/css/mdc.png" alt="Logo" class="mx-auto w-14 h-14 rounded-full ml-4" />
        <nav>
          <div class="flex ml-16 gap-10">
            <a href="/guest">Dashboard</a>
            <a href="/calendar">Calendar</a>
          </div>
        </nav>
      </div>

      <!-- Dropdown for login -->
      <div class="flex justify-end">
        <!-- Dropdown Button -->
        <button  @click="toggleLogIn" class="block space-x-2 px-4 py-2 bg-white text-black hover:bg-gray-200 rounded transition-transform duration-300 ease-in-out hover:scale-110">
          <i class="fas fa-user"></i>
          <span>Log In</span>
          
        </button>
        </div>

        <!-- Dropdown Menu -->
    </header>

    <main class="flex justify-center overflow-hidden">
      <slot />
    </main>
  </div>
</template>


<script>
const toggleLogIn = () => {
  document.getElementById('login').classList.remove('hidden');
};
</script>