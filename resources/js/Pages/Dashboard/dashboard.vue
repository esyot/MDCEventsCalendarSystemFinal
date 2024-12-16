<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });

const isHasRecord = (day, month, year, events) => {
    const validEvents = Array.isArray(events) ? events : [];
    const [newYear, newMonth] = month.split("-").map(Number);
    const date = new Date(year, newMonth - 1, day);

    const dateString = date.toISOString().split("T")[0];

    return validEvents.some((event) => {
        // Parse event start and end dates and reset their time to midnight (00:00:00)
        const eventStartDate = new Date(event.date_start);
        eventStartDate.setHours(0, 0, 0, 0);

        const eventEndDate = new Date(event.date_end);
        eventEndDate.setHours(23, 59, 59, 999); // Set the end date to the last moment of the day

        // Check if the given date falls within the event date range (inclusive)
        return date >= eventStartDate && date <= eventEndDate;
    });
};

const openSingleEvent = (id) => {
    document
        .getElementById("preview-event-single" + id)
        .classList.toggle("hidden");
};
</script>
<template>
    <div
        id="eventsDetails"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-40"
    >
        <div class="bg-white rounded shadow-md">
            <div
                class="flex justify-between items-center border-b border-gray-200"
            >
                <h1 class="text-xl px-2 font-semibold">
                    Events on
                    <span class="text-red-500">{{ dateSelected }}</span>
                </h1>
                <button
                    @click="closeEventsDetails()"
                    class="px-2 text-2xl font-bold hover:opacity-50"
                >
                    &times;
                </button>
            </div>

            <div class="">
                <table class="w-[700px] border-collapse">
                    <thead>
                        <tr class="w-full bg-gray-200">
                            <th
                                class="text-center font-medium text-gray-700 border-b"
                            >
                                Event Name
                            </th>
                            <th
                                class="text-center font-medium text-gray-700 border-b"
                            >
                                Date
                            </th>
                            <th
                                class="text-center font-medium text-gray-700 border-b"
                            >
                                Date End
                            </th>
                            <th
                                class="text-center font-medium text-gray-700 border-b"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="event in filteredEvents"
                            :key="event.id"
                            class="text-center hover:bg-gray-200 cursor-pointer"
                        >
                            <td>{{ event.name }}</td>
                            <td>{{ formatDate(event.date_start) }}</td>
                            <td>{{ formatDate(event.date_end) }}</td>
                            <td>
                                <button
                                    @click="openSingleEvent(event.event_id)"
                                    class="hover:opacity-50"
                                >
                                    <i class="fas fa-eye text-blue-500"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div v-for="event in filteredEvents" :key="event.id">
        <div
            :id="'preview-' + event.id"
            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
        >
            <div class="bg-white rounded p-2">
                <div>
                    <h1 class="text-xl font-semibold">Event Details</h1>
                </div>

                <div class="flex flex-col items-start">
                    <span><strong>Name:</strong> {{ event.name }}</span>

                    <span
                        ><strong>Department:</strong>
                        {{ event.department_acronyms }}
                    </span>
                    <span
                        ><strong>Term:</strong>
                        {{ event.term_name }}
                    </span>
                    <span>
                        <strong>Date Start:</strong>

                        {{ formatDate(event.date_start) }}
                        {{ formatTime(event.time_start) }}
                    </span>
                    <span
                        ><strong>Date End:</strong>
                        {{ formatDate(event.date_end) }}

                        {{ formatTime(event.time_end) }}
                    </span>
                    <span
                        ><strong>Venue:</strong> {{ event.venue_name }} at
                        {{ event.venue_building }}
                    </span>
                </div>

                <button
                    @click="openSingleEvent(event.id)"
                    class="mt-2 px-4 py-2 border border-gray-300 text-gray-800 rounded hover:opacity-50"
                >
                    Close
                </button>
            </div>
        </div>

        <div
            :id="'preview-event-single' + event.event_id"
            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
        >
            <div class="bg-white rounded p-2">
                <div>
                    <h1 class="text-xl font-semibold">Event Details</h1>
                </div>

                <div class="flex flex-col items-start">
                    <span><strong>Name:</strong> {{ event.name }}</span>

                    <span
                        ><strong>Department:</strong>
                        {{ event.department_acronyms }}
                    </span>
                    <span
                        ><strong>Term:</strong>
                        {{ event.term_name }}
                    </span>
                    <span>
                        <strong>Date Start:</strong>

                        {{ formatDate(event.date_start) }}
                        {{ formatTime(event.time_start) }}
                    </span>
                    <span
                        ><strong>Date End:</strong>
                        {{ formatDate(event.date_end) }}

                        {{ formatTime(event.time_end) }}
                    </span>
                    <span
                        ><strong>Venue:</strong> {{ event.venue_name }} at
                        {{ event.venue_building }}
                    </span>
                </div>

                <div class="flex justify-center">
                    <button
                        @click="openSingleEvent(event.event_id)"
                        class="mt-2 px-4 py-2 border border-gray-300 text-gray-800 rounded hover:opacity-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-grow p-6 space-x-2 overflow-y-auto">
        <div class="flex flex-col space-y-2 w-full">
            <div
                class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl"
            >
                <div
                    class="flex items-center bg-blue-400 rounded-t-lg justify-between border-b-2 border-gray-500 w-full p-2"
                >
                    <input
                        type="month"
                        v-model="currentMonth"
                        class="bg-transparent bg-white rounded border shadow-inner font-medium p-0.5 focus:outline-none"
                    />

                    <a
                        href="/calendar"
                        class="px-2 hover:opacity-100 opacity-50"
                    >
                        <i class="fas fa-maximize"></i>
                    </a>
                </div>
                <div class="w-full rounded-lg shadow-md">
                    <div
                        class="flex bg-blue-100 border-b-2 border-gray-500 justify-around px-4 sadow-md font-bold"
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
                                @click="
                                    [
                                        isHasRecord(
                                            day,
                                            currentMonth,
                                            currentYear,
                                            events
                                        )
                                            ? eventsDetails(
                                                  day + 1,
                                                  currentMonth,
                                                  currentYear,
                                                  eventsWithDetails
                                              )
                                            : '',
                                    ]
                                "
                                v-if="day"
                                :class="[
                                    isHasRecord(
                                        day,
                                        currentMonth,
                                        currentYear,
                                        events
                                    )
                                        ? 'bg-blue-500 hover:opacity-50 text-blue-100'
                                        : 'hover:bg-gray-300',
                                    'flex justify-center items-center text-2xl hover:opacity-50 cursor-pointer',
                                    isSunday(index) ? 'text-red-500' : '',
                                ]"
                            >
                                {{ day }}

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
                class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500 shadow-md"
            >
                <div
                    class="px-2 border-b-2 border-gray-500 bg-blue-400 rounded-t-lg text-gray-100"
                >
                    <h1 class="text-2xl font-medium">Today's Event</h1>
                </div>
                <div class="flex flex-col overflow-y-auto my-2">
                    <div
                        v-if="events_today.length == 0"
                        class="flex justify-center text-xl"
                    >
                        No Updates yet.
                    </div>
                    <div
                        v-for="(event, index) in events_today"
                        :key="index"
                        class="flex items-center shadow-md mx-2"
                    >
                        <div class="flex space-x-1">
                            <p class="font-bold">{{ event.name }}</p>

                            <span>at</span>

                            <span class="font-semibold">
                                {{ formatTime(event.time_start) }}
                            </span>
                            <span>-</span>
                            <span class="font-semibold">
                                {{ formatTime(event.time_end) }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex w-[800px] flex-col bg-gray-100 rounded-xl border-2 border-gray-500 shadow-md"
        >
            <div
                class="p-2 border-b-2 border-gray-500 rounded-t-lg bg-blue-400 text-gray-100"
            >
                <h1 class="text-2xl font-medium">Event Updates</h1>
            </div>
            <div class="h-full">
                <div class="flex flex-col h-[80vh] overflow-y-auto">
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
                        class="p-1 hover:bg-gray-200 cursor-pointer last:rounded-b-lg"
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
                                            event.isApprovedByVenueCoordinator ==
                                                null
                                        "
                                        class="text-lg font-medium"
                                    >
                                        New request
                                    </h1>

                                    <p class="truncate w-[300px]">
                                        <strong> {{ event.name }}</strong>
                                        {{ event.date_start }} at
                                        {{ formatTime(event.time_start) }} to
                                        {{ event.date_end }}
                                        {{ formatTime(event.time_end) }}
                                    </p>
                                </div>

                                <div class="flex justify-end">
                                    <small class="px-4">{{
                                        timeAgo(event.updated_at)
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
        class="flex fixed inset-0 bg-gray-800 justify-center items-center bg-opacity-50 z-50"
    >
        <div class="bg-white rounded">
            <div class="p-2">
                <p class="mb-2">
                    <strong>Title:</strong> {{ selectedEvent.name }}
                </p>

                <p class="mb-2">
                    <strong>Venue:</strong> {{ selectedEvent.venue_name }} at
                    {{ selectedEvent.venue_building }}
                </p>

                <p class="mb-2">
                    <strong>Date Start:</strong>
                    {{ formatDate(selectedEvent.date_start) }} at
                    {{ formatTime(selectedEvent.time_start) }}
                </p>

                <p class="mb-4">
                    <strong>Date End:</strong>
                    {{ formatDate(selectedEvent.date_end) }} at
                    {{ formatTime(selectedEvent.time_end) }}
                </p>

                <div class="flex justify-end items-center space-x-1">
                    <button
                        @click="closeModal"
                        class="p-2 border border-gray-300 text-gray-800 hover:opacity-50 rounded"
                    >
                        Close
                    </button>

                    <a
                        href="/eventRequest"
                        class="p-2 bg-blue-500 text-white hover:opacity-50 rounded"
                    >
                        View in Event Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const timeAgo = (dateString) => {
    const date = new Date(dateString);

    if (isNaN(date)) {
        return "Invalid date";
    }

    const now = new Date();
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
        events: {
            type: Array,
        },
        event_updates: {
            type: Array,
            default: () => [],
        },
        events_today: {
            type: Object,
            default: () => [],
        },
        eventsWithDetails: {
            type: Array,
        },
        user_role: {
            type: String,
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
            filteredEvents: [],
            dateSelected: "",
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
        formatDate(date) {
            const newdate = new Date(date);
            const formattedDate = newdate.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
            });

            return formattedDate;
        },

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
        eventsDetails(day, month, currentYear, eventsWithDetails) {
            if (!eventsWithDetails || eventsWithDetails.length === 0) {
                console.error("No events available.");
                // Make sure this is reactive
                this.filteredEvents = [];
                return;
            }

            // Correctly parse the month and year from the input string
            const [newYear, newMonth] = month.split("-").map(Number);

            // Create the Date object with proper year, month (0-based), and day
            const date = new Date(newYear, newMonth - 1, day);

            // Format the date as desired (Month Day, Year)
            const formattedDate = date.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            });

            this.dateSelected = formattedDate;
            this.daySelected = day;

            const formattedInputDate = date.toISOString().split("T")[0];

            this.filteredEvents = eventsWithDetails.filter((event) => {
                // Convert event's date_start and date_end to ISO format (YYYY-MM-DD)
                const eventStartDate = new Date(event.date_start)
                    .toISOString()
                    .split("T")[0];
                const eventEndDate = new Date(event.date_end)
                    .toISOString()
                    .split("T")[0];

                // Check if the selected date is within the event's date range (inclusive)
                return (
                    formattedInputDate >= eventStartDate &&
                    formattedInputDate <= eventEndDate
                );
            });

            document.getElementById("eventsDetails").classList.toggle("hidden");
        },
        closeEventsDetails() {
            document.getElementById("eventsDetails").classList.toggle("hidden");
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
