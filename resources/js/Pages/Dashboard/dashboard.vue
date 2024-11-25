<script setup>
import { useForm } from "@inertiajs/vue3";
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });
</script>
<template>
    <div class="flex flex-grow p-6 space-x-2 overflow-y-auto">
        <div class="flex flex-col space-y-2 w-full">
            <div
                class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl"
            >
                <div class="border-b-2 border-gray-500 w-full p-2">
                    <input
                        type="month"
                        v-model="currentMonth"
                        class="bg-transparent bg-white rounded p-2 border shadow-inner font-medium text-xl focus:outline-none"
                    />
                </div>
                <div class="w-full rounded-lg">
                    <!-- Header Row for Days -->
                    <div
                        class="flex bg-blue-300 justify-between px-4 sadow-md font-bold"
                    >
                        <div class="flex-1 text-center text-red-500">SUN</div>
                        <div class="flex-1 text-center">MON</div>
                        <div class="flex-1 text-center">TUE</div>
                        <div class="flex-1 text-center">WED</div>
                        <div class="flex-1 text-center">THU</div>
                        <div class="flex-1 text-center">FRI</div>
                        <div class="flex-1 text-center">SAT</div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1 p-1">
                        <div
                            v-for="(day, index) in daysInMonth"
                            :key="day ? day : Math.random()"
                            class="p-4"
                        >
                            <div
                                v-if="day"
                                :class="[
                                    'flex justify-center items-center text-2xl hover:opacity-50 cursor-pointer',
                                    isSunday(index) ? 'text-red-500' : '',
                                ]"
                            >
                                {{ day }}

                                <!-- Highlight current day, only for the current month -->
                                <i
                                    :class="
                                        isToday(day, currentMonth, currentYear)
                                            ? 'fas fa-circle text-[6px] absolute mt-10 text-green-500'
                                            : 'hidden'
                                    "
                                ></i>
                            </div>
                            <div v-else class=""></div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500"
            >
                <div class="px-2 border-b-2 border-gray-500">
                    <h1 class="text-2xl font-medium">Today's Event</h1>
                </div>
                <div class="flex flex-col overflow-y-auto my-2">
                    <div
                        v-if="event_updates.length == 0"
                        class="flex justify-center text-xl"
                    >
                        No Updates yet.
                    </div>
                    <div
                        v-for="(event, index) in events_today"
                        :key="index"
                        class="flex items-center shadow-md mx-2"
                    >
                        <div class="ml-6">
                            <p class="font-bold">{{ event.title }}</p>
                            <span
                                >{{ event.date }} at
                                {{ formatTime(event.time_start) }} -
                                {{ formatTime(event.time_end) }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex w-[800px] flex-col bg-gray-100 rounded-xl border-2 border-gray-500"
        >
            <div class="p-2 border-b-2 border-gray-500">
                <h1 class="text-2xl font-medium">Event Updates</h1>
            </div>
            <div class="">
                <div class="">
                    <div
                        v-if="event_updates.length == 0"
                        class="flex justify-center text-xl"
                    >
                        No Updates yet.
                    </div>

                    <div
                        v-for="(event, index) in event_updates"
                        :key="index"
                        title="Click to preview"
                        @click="preview(event)"
                        class="p-1 hover:bg-gray-200 cursor-pointer"
                    >
                        <div
                            class="flex justify-between space-x-2 items-center"
                        >
                            <i
                                class="fa-regular fa-bell fa-light fa-2xl px-2"
                            ></i>

                            <div
                                class="flex justify-between items-center w-full"
                            >
                                <div class="flex flex-col">
                                    <h1
                                        v-if="event.isApprovedByAdmin != null"
                                        class="text-lg font-medium"
                                    >
                                        New approved request
                                    </h1>

                                    <h1
                                        v-else="
                                            event.isApprovedByAdmin == null &&
                                            event.isApprovedByVenuCoordinator ==
                                                null
                                        "
                                        class="text-lg font-medium"
                                    >
                                        New request
                                    </h1>

                                    <p class="truncate w-[300px]">
                                        {{ event.name }}
                                        {{ event.date_start }} at
                                        {{ formatTime(event.time_start) }} to
                                        {{ event.date_end }}
                                        {{ formatTime(event.time_end) }}
                                    </p>
                                </div>

                                <div class="flex justify-end">
                                    <small class="px-4">{{
                                        timeAgo(event.created_at)
                                    }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="isModalOpen"
        class="flex fixed inset-0 bg-gray-800 justify-center items-center bg-opacity-50"
    >
        <div class="bg-white p-4 rounded">
            <p><strong>Title:</strong> {{ selectedEvent.name }}</p>

            <p>
                <strong>Venue:</strong> {{ selectedEvent.venue_name }} at
                {{ selectedEvent.venue_building }}
            </p>

            <p><strong>Date Start:</strong> {{ selectedEvent.date_start }}</p>
            <p>
                <strong>Time Start:</strong>
                {{ formatTime(selectedEvent.time_start) }}
            </p>
            <p><strong>Date Start:</strong> {{ selectedEvent.date_end }}</p>

            <p>
                <strong>Time End:</strong>
                {{ formatTime(selectedEvent.time_end) }}
            </p>
            <div class="flex justify-end items-center space-x-1">
                <button
                    @click="closeModal"
                    class="p-2 border border-gray-300 text-gray-800 rounded"
                >
                    Close
                </button>

                <a
                    href="/eventRequest"
                    class="p-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                >
                    View
                </a>
            </div>
        </div>
    </div>
</template>

<script>
const timeAgo = (dateString) => {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) {
        return `${diffInSeconds} sec${diffInSeconds !== 1 ? "s" : ""} ago`;
    }

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `${diffInMinutes} min${diffInMinutes !== 1 ? "s" : ""} ago`;
    }

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `${diffInHours} hr${diffInHours !== 1 ? "s" : ""} ago`;
    }

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) {
        return `${diffInDays} day${diffInDays !== 1 ? "s" : ""} ago`;
    }

    const diffInMonths = Math.floor(diffInDays / 30);
    if (diffInMonths < 12) {
        return `${diffInMonths} mon${diffInMonths !== 1 ? "s" : ""} ago`;
    }

    const diffInYears = Math.floor(diffInMonths / 12);
    return `${diffInYears} yr${diffInYears !== 1 ? "s" : ""} ago`;
};

export default {
    props: {
        event_updates: {
            type: Array,
            default: () => [],
        },
        events_today: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        const today = new Date();
        const month = today.getMonth();
        const year = today.getFullYear();
        return {
            currentMonth: `${year}-${(month + 1).toString().padStart(2, "0")}`,
            currentYear: year,
            isModalOpen: false,
            selectedEvent: null,
        };
    },
    computed: {
        daysInMonth() {
            const [year, month] = this.currentMonth.split("-").map(Number);
            const date = new Date(year, month - 1, 1);
            const days = [];
            const firstDay = date.getDay();
            const lastDate = new Date(year, month, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                days.push(null);
            }

            for (let i = 1; i <= lastDate; i++) {
                days.push(i);
            }

            return days;
        },
    },
    methods: {
        formatTime(time) {
            const [hours, minutes] = time.split(":");
            const formattedHours = hours % 12 || 12;
            const ampm = hours < 12 ? "am" : "pm";
            return `${formattedHours}:${minutes} ${ampm}`;
        },
        preview(event) {
            this.selectedEvent = event;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.selectedEvent = null;
        },
        isSunday(index) {
            return index % 7 === 0;
        },
        isToday(day) {
            const today = new Date();
            const [year, month] = this.currentMonth.split("-").map(Number); // Extract year and month from currentMonth

            return (
                today.getDate() === day && // Check if today matches the day
                today.getMonth() === month - 1 && // Check if today matches the selected month (month - 1 because months are 0-indexed)
                today.getFullYear() === year // Check if today matches the selected year
            );
        },
    },
    mounted() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, "0");
        this.currentMonth = `${year}-${month}`;
    },
};
</script>
