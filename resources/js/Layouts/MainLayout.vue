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
                <h1 class="text-center font-semibold">MDC - SCHOOL CALENDAR</h1>
            </div>

            <nav class="flex flex-col gap-4 text-sm">
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
            <nav class="bg-white shadow p-2">
                <div class="flex justify-between items-center">
                    <h1 class="text-lg font-bold">{{ pageTitle }}</h1>
                    <div>
                        <div class="relative inline-block text-left">
                            <button
                                @click="toggleDropdown"
                                class="flex items-center space-x-2 hover:opacity-50"
                            >
                                <i class="fas fa-user fa-sm"></i>
                                <span class="text-sm"
                                    >{{ user.lname }}, {{ user.fname }}
                                </span>
                                <i v-if="!isOpen" class="fas fa-chevron-down">
                                </i>
                                <i v-if="isOpen" class="fas fa-chevron-up"></i>
                            </button>

                            <div
                                v-if="isOpen"
                                class="origin-top-right absolute right-0 mt-2 py-2 w-[100px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                            >
                                <div
                                    class="flex flex-col items-center space-y-2"
                                >
                                    <button
                                        class="hover:opacity-50"
                                        onclick="document.getElementById('logout-confirm').classList.toggle('hidden');"
                                    >
                                        Log out
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <div
                id="logout-confirm"
                class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden"
            >
                <div class="bg-white rounded shadow-md">
                    <header>
                        <h1 class="p-4 text-lg">Are you sure to Log out?</h1>
                    </header>
                    <footer class="flex justify-end space-x-1 p-2">
                        <button
                            type="button"
                            onclick="document.getElementById('logout-confirm').classList.toggle('hidden');"
                            class="px-4 py-2 border border-gray-300 rounded text-gray-800 hover:opacity-50 rounded"
                        >
                            No
                        </button>
                        <a
                            href="/logout"
                            class="px-4 py-2 bg-red-500 text-red-100 hover:opacity-50 rounded"
                            >Yes</a
                        >
                    </footer>
                </div>
            </div>
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
