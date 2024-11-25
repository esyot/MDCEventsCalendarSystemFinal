<script setup>
import { ref } from "vue";

const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="flex h-screen">
        <aside
            class="bg-gradient-to-b select-none from-blue-500 to-blue-900 text-white p-4 w-26 h-full shadow flex flex-col"
        >
            <div class="flex flex-col items-center justify-center mb-8 gap-4">
                <img
                    src="/resources/css/mdc.png"
                    alt="Logo"
                    class="w-14 h-14 rounded-full shadow-md"
                    style="margin-top: -4px"
                />
                <hr class="border-t-1 border-white w-full" />
            </div>

            <nav class="flex flex-col gap-4">
                <a href="/dashboard" class="hover:bg-blue-700 p-2 rounded">
                    <i class="fas fa-gauge"></i>
                    Dashboard</a
                >
                <a href="/calendar" class="hover:bg-blue-700 p-2 rounded">
                    <i class="fas fa-calendar"></i>
                    Calendar</a
                >
                <a href="/eventRequest" class="hover:bg-blue-700 p-2 rounded">
                    <span v-if="user_role == 'venue_coordinator'">
                        <i class="fa-solid fa-location-arrow"></i>
                        Venue Request</span
                    >
                    <span v-else>
                        <i class="fa-solid fa-location-arrow"></i>
                        Event Request
                    </span>
                </a>

                <a
                    v-if="user_role == 'super_admin' || user_role == 'admin'"
                    href="/venue-coordinators"
                    class="hover:bg-blue-700 p-2 rounded"
                >
                    <i class="fa-solid fa-users"></i>
                    Venue Coordinators</a
                >

                <a
                    href="/users"
                    v-if="user_role == 'super_admin' || user_role == 'admin'"
                    class="hover:bg-blue-700 p-2 rounded"
                >
                    <i class="fa-solid fa-users-cog"></i>
                    Users</a
                >
            </nav>
        </aside>

        <main class="w-full select-none overflow-hidden bg-gray-200">
            <nav class="bg-white shadow p-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-lg font-bold">{{ pageTitle }}</h1>
                    <div>
                        <div class="relative inline-block text-left">
                            <button
                                @click="toggleDropdown"
                                class="flex items-center space-x-2 hover:opacity-50"
                            >
                                <i class="fas fa-user"></i>
                                <span>{{ user.lname }}, {{ user.fname }} </span>
                                <i v-if="!isOpen" class="fas fa-chevron-down">
                                </i>
                                <i v-if="isOpen" class="fas fa-chevron-up"></i>
                            </button>

                            <div
                                v-if="isOpen"
                                class="origin-top-right absolute right-0 mt-2 py-2 w-[100px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                            >
                                <div
                                    class="flex flex-col items-center space-y-2"
                                >
                                    <a href="/logout" class="hover:opacity-50"
                                        >Logout</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <section>
                <slot />
            </section>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        user: {
            type: Object,
        },
        pageTitle: String,
        user_role: {
            type: String,
        },
    },
    data() {
        return {
            event: {
                approval: true,
                approval2: true,
            },
        };
    },
    methods: {},
};
</script>
