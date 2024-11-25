<script setup>
import { ref } from "vue";

const isOpen = ref(false);
</script>

<template>
    <div class="">
        <header
            class="bg-blue-800 text-white p-2 flex items-center justify-between shadow z-50"
        >
            <div class="flex items-center">
                <nav class="flex items-center space-x-4">
                    <img
                        src="/resources/css/mdc.png"
                        alt="Logo"
                        class="rounded-full shadow-md w-14 h-14"
                    />
                    <div class="flex space-x-4">
                        <a href="/guest-dashboard" class="hover:opacity-50"
                            >Dashboard</a
                        >
                        <a href="/guest-calendar" class="hover:opacity-50"
                            >Calendar</a
                        >
                    </div>
                </nav>
            </div>

            <div class="flex justify-end">
                <button
                    @click="toggleLogIn"
                    class="block space-x-2 px-4 py-2 bg-white text-black hover:bg-gray-200 rounded hover:opacity-50"
                >
                    <i class="fas fa-user"></i>
                    <span>Log In</span>
                </button>
            </div>
        </header>

        <main class="flex justify-center overflow-hidden">
            <slot />
        </main>
    </div>
</template>

<script>
const toggleLogIn = () => {
    document.getElementById("login").classList.remove("hidden");
};
</script>
