<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });
</script>

<template>
    <div class="flex flex-grow p-6 space-x-2">
        <div class="flex flex-col space-y-2 w-full">
            <div
                class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl"
            >
                <div class="border-b-2 border-gray-500 w-full p-4">
                    <input
                        type="month"
                        v-model="currentMonth"
                        class="bg-transparent bg-white rounded p-2 shadow-md font-medium text-xl focus:outline-none"
                    />
                </div>

                <div class="w-full h-full rounded-lg">
                    <div
                        class="flex bg-blue-300 justify-between px-4 font-bold"
                    >
                        <div>SUN</div>
                        <div>MON</div>
                        <div>TUE</div>
                        <div>WED</div>
                        <div>THU</div>
                        <div>FRI</div>
                        <div>SAT</div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 p-1">
                        <template v-for="day in daysInMonth">
                            <div
                                v-if="day"
                                class="flex justify-center items-center h-16 border border-gray-400"
                            >
                                {{ day }}
                            </div>
                            <div
                                v-else
                                class="h-16 border border-gray-400"
                            ></div>
                        </template>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500"
            >
                <div class="flex flex-col p-4 border-b-2 border-gray-500">
                    <h1 class="text-2xl font-medium">Today's Event</h1>
                </div>
                <div class="flex flex-col overflow-y-auto my-2">
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
            class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500"
        >
            <div class="p-4 border-b-2 border-gray-500">
                <h1 class="text-2xl font-medium">Event Updates</h1>
            </div>
            <div class="flex flex-col space-y-2">
                <div
                    class="flex flex-col items-center justify-center mx-4 my-2 space-y-2"
                >
                    <div
                        v-for="(event, index) in events"
                        :key="index"
                        title="Click to preview"
                        @click="preview(event)"
                        class="flex items-center p-2 shadow-md w-[400px] justify-center hover:opacity-50 cursor-pointer transition-transform duration-300 ease-in-out hover:scale-90"
                    >
                        <i class="fa-regular fa-bell fa-light fa-2xl"></i>
                        <div class="ml-6">
                            <p class="font-bold">{{ event.name }}</p>
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
    </div>

    <div
        v-if="isModalOpen"
        class="flex fixed inset-0 bg-gray-800 justify-center items-center bg-opacity-50"
    >
        <div class="bg-white p-4 rounded">
            <p><strong>Title:</strong> {{ selectedEvent.name }}</p>
            <!-- <p><strong>Description:</strong> {{ selectedEvent.description }}</p> -->
            <p>
                <strong>Date Start:</strong>
                {{ formatTime(selectedEvent.time_start) }}
            </p>
            <p>
                <strong>Date End:</strong>
                {{ formatTime(selectedEvent.time_end) }}
            </p>
            <div class="flex justify-end">
                <button
                    @click="closeModal"
                    class="mt-4 p-2 bg-blue-500 text-white rounded"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        events: {
            type: Array,
            default: () => [],
        },
        events_today: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            currentMonth: new Date().toISOString().substr(0, 7),
            isModalOpen: false,
            selectedEvent: null,
        };
    },
    computed: {
        daysInMonth() {
            const [year, month] = this.currentMonth.split("-").map(Number);
            const date = new Date(year, month, 1);
            const days = [];
            const firstDay = date.getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();

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
            this.selectedEvent = event; // Set the selected event
            this.isModalOpen = true; // Open the modal
        },
        closeModal() {
            this.isModalOpen = false; // Close the modal
            this.selectedEvent = null; // Clear the selected event
        },
    },
    mounted() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, "0");
        this.currentMonth = `${year}-${month}`;
    },
};
</script>
