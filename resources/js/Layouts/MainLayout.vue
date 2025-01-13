<script setup>
import { ref, onMounted } from "vue";

const isSidebarOpen = ref(localStorage.getItem("sidebarState") === "true");
const isDropdownOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;

    localStorage.setItem("sidebarState", isSidebarOpen.value.toString());
};

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
</script>

<template>
    <div class="flex h-screen">
        <aside
            :class="[
                'bg-gradient-to-b select-none from-blue-600 to-blue-900 text-white p-4 h-full shadow flex flex-col transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'w-64' : 'w-20',
            ]"
        >
            <div class="flex flex-col items-center w-full justify-center mb-4">
                <div>
                    <img
                        src="/resources/css/mdc.png"
                        alt="Logo"
                        class="w-14 rounded-full shadow-md"
                    />
                </div>

                <hr class="border-t opacity-30 mt-2 border-white w-full" />
                <h1
                    v-if="isSidebarOpen"
                    class="mt-2 text-center font-semibold text-sm"
                >
                    MDC - SCHOOL EVENT CALENDAR
                </h1>
            </div>
            <div
                class="mb-2"
                :class="isSidebarOpen ? '' : 'flex justify-center'"
            >
                <span class="text-xs text-center opacity-50 font-medium">
                    {{
                        user_role
                            .split("_")
                            .map(
                                (word) =>
                                    word.charAt(0).toUpperCase() + word.slice(1)
                            )
                            .join(" ")
                    }}
                </span>
            </div>
            <hr class="border-t opacity-30 border-white w-full" />

            <nav
                :class="isSidebarOpen ? 'items-start' : 'items-center'"
                class="flex flex-col gap-2 text-sm"
            >
                <a
                    href="/dashboard"
                    :class="[
                        isSidebarOpen ? '' : 'flex flex-col text-xs',
                        pageTitle == 'Dashboard'
                            ? 'opacity-100 hover:opacity-60'
                            : 'opacity-60 hover:opacity-100',
                        'p-2 rounded flex  items-center w-full text-white',
                    ]"
                >
                    <i
                        class="fas fa-gauge text-sm mr-2 transition-transform transform duration-300 underline-none"
                        :class="isSidebarOpen ? 'text-xl' : 'text-xl scale-90'"
                    ></i>
                    <span :class="pageTitle == 'Dashboard' ? 'underline' : ''"
                        >Dashboard</span
                    >
                </a>
                <a
                    href="/calendar"
                    :class="[
                        isSidebarOpen ? '' : 'flex flex-col text-xs',
                        pageTitle == 'Calendar'
                            ? 'opacity-100 hover:opacity-60'
                            : 'opacity-60 hover:opacity-100',
                        'p-2 rounded flex  items-center w-full text-white',
                    ]"
                    class="p-2 rounded flex items-center w-full text-white"
                >
                    <i
                        class="fas fa-calendar text-sm mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-xl' : 'text-xl scale-90'"
                    ></i>
                    <span :class="pageTitle == 'Calendar' ? ' underline' : ''"
                        >Calendar</span
                    >
                </a>

                <a
                    href="/venues"
                    :class="[
                        isSidebarOpen ? '' : 'flex flex-col text-xs',
                        pageTitle == 'Venues'
                            ? 'opacity-100 hover:opacity-60'
                            : 'opacity-60 hover:opacity-100',
                        'p-2 rounded flex  items-center w-full text-white',
                    ]"
                    v-if="
                        user_role == 'venue_coordinator' ||
                        user_role == 'sec-admin'
                    "
                    class="p-2 rounded flex items-center w-full text-white"
                >
                    <i
                        class="fa-solid fa-building text-sm mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-xl' : 'text-xl scale-90'"
                    ></i>
                    <span :class="pageTitle == 'Venues' ? ' underline' : ''"
                        >Venues</span
                    >
                </a>

                <a href="/eventRequest" class="w-full">
                    <span
                        v-if="user_role === 'venue_coordinator'"
                        :class="[
                            isSidebarOpen
                                ? ''
                                : 'flex flex-col text-xs text-center',
                            pageTitle == 'Requests'
                                ? 'opacity-100 hover:opacity-60'
                                : 'opacity-60 hover:opacity-100',
                            'p-2 rounded flex  items-center w-full text-white',
                        ]"
                        class="p-2 rounded flex items-center w-full text-white"
                    >
                        <i
                            class="fa-solid fa-location-arrow text-sm mr-2 transition-transform transform"
                            :class="
                                isSidebarOpen ? 'text-xl' : 'text-xl scale-90'
                            "
                        ></i>
                        <span
                            :class="pageTitle == 'Requests' ? ' underline' : ''"
                            >Venue Request</span
                        >
                    </span>
                    <span
                        v-else
                        :class="[
                            isSidebarOpen
                                ? ''
                                : 'flex flex-col text-xs text-center',
                            pageTitle == 'Requests'
                                ? 'opacity-100 hover:opacity-60'
                                : 'opacity-60 hover:opacity-100',
                            'p-2 rounded flex  items-center w-full text-white',
                        ]"
                        class="p-2 rounded flex items-center w-full text-white"
                    >
                        <i
                            class="fa-solid fa-location-arrow text-sm mr-2 transition-transform transform"
                            :class="
                                isSidebarOpen ? 'text-xl' : 'text-xl scale-90'
                            "
                        ></i>
                        <span
                            :class="pageTitle == 'Requests' ? ' underline' : ''"
                            >Event Request</span
                        >
                    </span>
                </a>

                <a
                    v-if="user_role === 'admin' || user_role === 'sec-admin'"
                    href="/venue-coordinators"
                    :class="[
                        isSidebarOpen
                            ? ''
                            : 'flex flex-col text-xs text-center',
                        pageTitle == 'List of Venue Coordinators'
                            ? 'opacity-100 hover:opacity-60'
                            : 'opacity-60 hover:opacity-100',
                    ]"
                    class="p-2 rounded flex items-center w-full text-white"
                >
                    <i
                        class="fa-solid fa-users text-sm mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-xl' : 'text-xl scale-90'"
                    ></i>
                    <span
                        :class="
                            pageTitle == 'List of Venue Coordinators'
                                ? ' underline'
                                : ''
                        "
                        >Venue Coordinators</span
                    >
                </a>

                <a
                    v-if="user_role === 'admin' || user_role === 'sec-admin'"
                    href="/users"
                    :class="[
                        isSidebarOpen
                            ? ''
                            : 'flex flex-col text-xs text-center',
                        pageTitle == 'List of Users'
                            ? 'opacity-100 hover:opacity-60'
                            : 'opacity-60 hover:opacity-100',
                    ]"
                    class="p-2 rounded flex items-center w-full text-white"
                >
                    <i
                        class="fa-solid fa-users-cog text-sm mr-2 transition-transform transform"
                        :class="isSidebarOpen ? 'text-xl' : 'text-xl scale-90'"
                    ></i>
                    <span
                        :class="
                            pageTitle == 'List of Users' ? ' underline' : ''
                        "
                        >Users</span
                    >
                </a>
            </nav>
            <div class="flex w-full justify-center">
                <button
                    @click="toggleSidebar"
                    class="mt-5 hover:opacity-70 px-3 py-1 bg-gray-400 opacity-50 rounded-full text-gray-100"
                >
                    <i
                        :class="
                            isSidebarOpen
                                ? 'fas fa-chevron-left fa-xs'
                                : 'fas fa-chevron-right fa-xs'
                        "
                    ></i>
                </button>
            </div>
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
                                        <i class="fas fa-sign-out"></i>
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
                <div class="bg-white rounded shadow-md p-4">
                    <header>
                        <h1 class="text-xl">Are you sure to Log out?</h1>
                    </header>
                    <footer class="flex mt-4 justify-end space-x-1 p-1">
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
