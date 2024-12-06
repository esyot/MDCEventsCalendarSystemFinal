<script setup>
import { ref, onMounted } from "vue";

// Initialize the sidebar state from localStorage (if available)
const isSidebarOpen = ref(localStorage.getItem("sidebarState") === "true");
const isDropdownOpen = ref(false);

// Function to toggle sidebar and save state to localStorage
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    // Save the state to localStorage
    localStorage.setItem("sidebarState", isSidebarOpen.value.toString());
};

// Function to toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside
            :class="[
                'bg-gradient-to-b select-none from-blue-500 to-blue-900 text-white p-4 h-full shadow flex flex-col',
                isSidebarOpen ? 'w-64' : 'w-20',
            ]"
        >
            <div class="flex flex-col items-center justify-center mb-8">
                <div>
                    <img
                        src="/resources/css/mdc.png"
                        alt="Logo"
                        class="w-20 rounded-full shadow-md"
                    />
                </div>

                <hr class="border-t-2 mt-2 border-white w-full" />
                <h1
                    v-if="isSidebarOpen"
                    class="text-center font-semibold text-lg"
                >
                    MDC - SCHOOL CALENDAR
                </h1>
            </div>

            <nav
                :class="isSidebarOpen ? 'items-start' : 'items-center'"
                class="flex flex-col gap-6 text-sm"
            >
                <a
                    href="/dashboard"
                    class="hover:bg-blue-700 p-2 rounded flex items-center"
                >
                    <i
                        class="fas fa-gauge mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'"
                    ></i>
                    <span v-if="isSidebarOpen">Dashboard</span>
                </a>

                <a
                    href="/calendar"
                    class="hover:bg-blue-700 p-2 rounded flex items-center"
                >
                    <i
                        class="fas fa-calendar mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'"
                    ></i>
                    <span v-if="isSidebarOpen">Calendar</span>
                </a>

                <a
                    href="/eventRequest"
                    class="hover:bg-blue-700 p-2 rounded flex items-center"
                >
                    <span v-if="user_role === 'venue_coordinator'">
                        <i
                            class="fa-solid fa-location-arrow mr-2 transition-transform transform"
                            :class="
                                isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'
                            "
                        ></i>
                        <span v-if="isSidebarOpen">Venue Request</span>
                    </span>
                    <span v-else>
                        <i
                            class="fa-solid fa-location-arrow mr-2 transition-transform transform"
                            :class="
                                isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'
                            "
                        ></i>
                        <span v-if="isSidebarOpen">Event Request</span>
                    </span>
                </a>

                <a
                    v-if="user_role === 'super_admin' || user_role === 'admin'"
                    href="/venue-coordinators"
                    class="hover:bg-blue-700 p-2 rounded flex items-center"
                >
                    <i
                        class="fa-solid fa-users mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'"
                    ></i>
                    <span v-if="isSidebarOpen">Venue Coordinators</span>
                </a>

                <a
                    v-if="user_role === 'super_admin' || user_role === 'admin'"
                    href="/users"
                    class="hover:bg-blue-700 p-2 rounded flex items-center"
                >
                    <i
                        class="fa-solid fa-users-cog mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-2xl' : 'text-xl scale-90'"
                    ></i>
                    <span v-if="isSidebarOpen">Users</span>
                </a>
            </nav>

            <!-- Toggle Sidebar Button -->
            <button
                @click="toggleSidebar"
                class="mt-5 hover:bg-gray-400 p-4 rounded-full text-white"
            >
                <i
                    :class="
                        isSidebarOpen
                            ? 'fas fa-chevron-left'
                            : 'fas fa-chevron-right'
                    "
                ></i>
            </button>
        </aside>

        <!-- Main Content -->
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
                                    >{{ user.lname }}, {{ user.fname }}</span
                                >
                                <i
                                    v-if="!isDropdownOpen"
                                    class="fas fa-chevron-down"
                                ></i>
                                <i
                                    v-if="isDropdownOpen"
                                    class="fas fa-chevron-up"
                                ></i>
                            </button>

                            <div
                                v-if="isDropdownOpen"
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
};
</script>
