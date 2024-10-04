<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

// Function to toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};



</script>

<template>
    <div class="flex">
        <!-- Sidebar -->
        <aside class="bg-blue-800 text-white p-4 w-26 h-screen shadow flex flex-col">
            <div class="flex flex-col items-center justify-center mb-8 gap-4">
                <img src="/resources/css/mdc.png" alt="Logo" class="w-14 h-14 rounded-full" style="margin-top: -4px;"  /> 
                <!-- Horizontal line added below the image -->
                <hr class="border-t-1 border-white w-full">
            </div>
        
            <nav class="flex flex-col gap-4">
                <a  href="/dashboard" class="hover:bg-blue-700 p-2 rounded">Dashboard</a>
               
                
                <a  href="/calendar" class="hover:bg-blue-700 p-2 rounded">Calendar</a>


                <!-- <a v-if="hasPermission('can create event')" href="/createEvent" class="hover:bg-blue-700 p-2 rounded">Create Event</a> -->
                <a v-if="hasPermission('can request event')" href="/eventRequest" class="hover:bg-blue-700 p-2 rounded">Event Request</a>
                <a v-if="hasPermission('can view users')" href="/users" class="hover:bg-blue-700 p-2 rounded">Users</a>
            </nav>
        </aside>

        <!-- <div :class="event.approval && event.approval2 == true ? 'bg-blue-500' : 'bg-gray-300'" class="flex fixed inset-0 justify-center items-center">

            hiiiiii
             
        </div> -->

        <!-- Mao ni ang Main Content -->
        <main class="flex-1 bg-gray-200 min-h-screen">
            <nav class="bg-white shadow  p-3">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-lg font-bold">{{ pageTitle }}</h1>
                    <div>
                        <div class="relative inline-block text-left">
                            <!-- Dropdown Button -->
                            <button @click="toggleDropdown" class="inline-flex items-center space-x-2 justify-center w-full rounded-md border border-gray-300 shadow-sm px-7 py-0.5 bg-white text-xs hover:bg-gray-50 focus:outline-none">
                               <i class="fas fa-user"></i>
                                <span>{{ name }}</span>
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
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </nav>
            
            <slot />
        </main>
    </div>
</template>

<style scoped>
a {
    transition: background-color 0.3s ease;
}


</style>

<script>
export default {
  props: {
    userPermissions: {
      type: Array,
      default: () => [], // Ensures it's always an array
    },
    user: Number,
    pageTitle: String,
    name: String,
    users: {
        type: Array,

    }
  },
  data() {
    return {
      event: {
        approval: true,    // Change this as needed
        approval2: true,  
        
       // Change this as needed
      },
    };
  },
  methods: {
    hasPermission(permission) {
      return this.userPermissions.includes(permission);
    },
  },
};
</script>
