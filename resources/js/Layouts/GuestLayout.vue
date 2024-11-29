<script setup>
import { ref } from "vue";

const isOpen = ref(false);
</script>

<template>
    <div class="shadow-md h-screen">
        <header
            class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-2 flex items-center justify-between shadow z-50"
        >
            <div class="flex items-center">
                <nav class="flex items-center space-x-4 p-1">
                    <img
                        src="/resources/css/mdc.png"
                        alt="Logo"
                        class="rounded-full shadow-md w-10 h-10"
                    />
                    <div class="flex space-x-6">
                        <a href="/guest-dashboard" class="hover:opacity-50"
                            >Dashboard</a
                        >
                        <div class="border-l border-gray-300 h-6"></div>

                        <a href="/guest-calendar" class="hover:opacity-50"
                            >Calendar</a
                        >
                    </div>
                </nav>
            </div>

            <div class="flex justify-end px-4 drop-shadow">
                <button
                    @click="toggleLogIn"
                    class="flex items-center space-x-2 hover:opacity-50"
                >
                    <i class="fas fa-user"></i>
                    <span>Log In</span>
                </button>
            </div>
        </header>

        <slot />
    </div>
</template>

<script>
const toggleLogIn = () => {
    document.getElementById("login").classList.remove("hidden");
};
</script>
