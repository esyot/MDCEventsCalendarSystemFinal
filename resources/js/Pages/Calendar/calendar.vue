<script setup>
import { ref, watch } from "vue";
import MainLayout from "../../Layouts/MainLayout.vue";
import { Inertia } from "@inertiajs/inertia";

defineOptions({ layout: MainLayout });

const currentDate = new Date();
const currentYear = ref(currentDate.getFullYear());
const currentMonth = ref(currentDate.getMonth() + 1);
const currentDay = ref(currentDate.getDate());

const isToday = (month, day) => {
    return (
        currentYear.value === currentDate.getFullYear() &&
        month === currentMonth.value &&
        day === currentDay.value
    );
};

const monthsInYear = Array.from({ length: 12 }, (_, index) => index + 1);

const daysInMonth = (month, year) => {
    return new Date(year, month, 0).getDate();
};

const getFirstDayOfMonth = (month, year) => {
    return new Date(year, month - 1, 1).getDay();
};

const calendarData = ref(
    monthsInYear.map((month) => {
        const days = daysInMonth(month, currentYear.value);
        const firstDay = getFirstDayOfMonth(month, currentYear.value);
        const monthDays = Array.from({ length: days }, (_, index) => index + 1);

        return Array(firstDay).fill(null).concat(monthDays);
    })
);

const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

const updateCalendarDataForYear = () => {
    calendarData.value = monthsInYear.map((month) => {
        const days = daysInMonth(month, currentYear.value);
        const firstDay = getFirstDayOfMonth(month, currentYear.value);
        const monthDays = Array.from({ length: days }, (_, index) => index + 1);

        return Array(firstDay).fill(null).concat(monthDays);
    });
};

const changeYear = (direction) => {
    if (direction === "prev" && currentYear.value > 2020) {
        currentYear.value--;
    } else if (direction === "next" && currentYear.value < 2030) {
        currentYear.value++;
    }
    updateCalendarDataForYear();
};

const changeMonthYear = (direction) => {
    if (direction === "prev" && currentYear.value != 2020) {
        selectedMonth.value = 11;
        currentYear.value--;
    } else if (direction === "next" && currentYear.value != 2030) {
        selectedMonth.value = 0;
        currentYear.value++;
    }
    updateCalendarDataForYear();
};

const changeMonth = (direction) => {
    if (direction === "prev") {
        selectedMonth.value--;
    } else if (direction === "next") {
        selectedMonth.value++;
    }
    updateCalendarDataForYear();
};

const selectedMonth = ref(null);
const searchQuery = ref("");
const selectedDepartment = ref("");

const selectMonth = (monthIndex) => {
    selectedMonth.value = monthIndex;
};

const backToYearView = () => {
    selectedMonth.value = null;
};

const closeSuccessMessage = () => {
    document.getElementById("successMessage").classList.toggle("hidden");
};

const closeErrorMessage = () => {
    document.getElementById("errorMessage").classList.toggle("hidden");
};

const isHasRecord = (day, month, year, events) => {
    const validEvents = Array.isArray(events) ? events : [];

    const date = new Date(year, month - 1, day);
    date.setHours(0, 0, 0, 0);

    return validEvents.some((event) => {
        const eventStartDate = new Date(event.date_start);
        eventStartDate.setHours(0, 0, 0, 0);

        const eventEndDate = new Date(event.date_end);
        eventEndDate.setHours(23, 59, 59, 999);

        return date >= eventStartDate && date <= eventEndDate;
    });
};

const openSingleEvent = (id) => {
    document.getElementById("preview-" + id).classList.toggle("hidden");
};

const openSingleSearchedEvent = (id) => {
    document
        .getElementById("preview-searched-" + id)
        .classList.toggle("hidden");
};

const convertToDate = (month, day, year) => {
    const date = new Date(year, month - 1, day);
    const formattedYear = date.getFullYear();
    const formattedMonth = String(date.getMonth() + 1).padStart(2, "0");
    const formattedDay = String(date.getDate()).padStart(2, "0");

    return `${formattedYear}-${formattedMonth}-${formattedDay}`;
};
</script>

<template>
    <div
        v-if="successMessage != null"
        id="successMessage"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white p-2 shadow-md rounded">
            <div class="flex flex-col items-center">
                <div class="p-2">
                    <i class="fas fa-circle-check text-green-500 fa-2xl"></i>
                </div>
                <div>{{ successMessage }}</div>

                <div>
                    <button
                        @click="closeSuccessMessage()"
                        class="px-4 py-2 border text-gray-800 border-gray-300 rounded hover:opacity-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="errorMessage != null"
        id="errorMessage"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white p-2 shadow-md rounded">
            <div class="flex flex-col items-center">
                <div class="p-2">
                    <i class="fas fa-times-circle text-red-500 fa-2xl"></i>
                </div>
                <div>{{ errorMessage }}</div>

                <div>
                    <button
                        @click="closeErrorMessage()"
                        class="px-4 py-2 border text-gray-800 border-gray-300 rounded hover:opacity-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        id="eventsDetails"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-40"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6">
            <div
                class="flex justify-between items-center mb-4 border-b border-gray-200"
            >
                <h1 class="text-2xl font-semibold">
                    Events on
                    <span class="text-red-500">{{ dateSelected }}</span>
                </h1>
                <button
                    @click="closeEventsDetails()"
                    class="text-2xl font-bold text-gray-600 hover:text-gray-900"
                >
                    &times;
                </button>
            </div>

            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-500 text-white">
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Event Name
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Date Start
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Date End
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Departments
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Status
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="event in filteredEvents"
                        :key="event.id"
                        :style="
                            'background-color:' +
                            departmentColor(event.department_id)
                        "
                        class="text-center hover:opacity-50 cursor-pointer"
                    >
                        <td class="text-left px-4 py-2">{{ event.name }}</td>
                        <td>
                            {{ formatDate(event.date_start) }}
                            {{ formatTime(event.time_start) }}
                        </td>
                        <td>
                            {{ formatDate(event.date_end) }}
                            {{ formatTime(event.time_end) }}
                        </td>
                        <td>{{ event.department_acronyms }}</td>
                        <td
                            class="flex items-center px-4 py-2 space-x-2 text-center"
                        >
                            <small>Venue:</small>
                            <i
                                :class="
                                    event.isApprovedByVenueCoordinator != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                            <small>Admin:</small>
                            <i
                                :class="
                                    event.isApprovedByAdmin != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                        </td>
                        <td class="px-4 py-2">
                            <button
                                @click="openSingleEvent(event.id)"
                                class="text-blue-500 hover:opacity-75"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-end p-2">
                <button
                    v-if="user_role != 'venue_coordinator'"
                    :class="checkDateNow(dateSelected) ? 'hidden' : ''"
                    @click="openCreateEventModal(daySelected)"
                    class="px-4 py-2 bg-blue-500 text-blue-100 rounded hover:opacity-50"
                >
                    Add Event
                </button>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div
        v-for="event in filteredEvents"
        :key="'preview-' + event.id"
        :id="'preview-' + event.id"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
    >
        <div class="bg-white rounded-lg shadow-lg p-4 max-w-lg w-full">
            <div class="mb-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    Event Details
                </h1>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-semibold">Name:</span>
                    <span>{{ event.name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Department:</span>
                    <span>{{ event.department_acronyms }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="font-semibold">Term:</span>
                    <span>{{ event.term_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Date Start:</span>
                    <span>
                        {{ formatDate(event.date_start) }} at
                        {{ formatTime(event.time_start) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Date End:</span>
                    <span>
                        {{ formatDate(event.date_end) }} at
                        {{ formatTime(event.time_end) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Venue:</span>
                    <span>
                        {{ event.venue_name }} at {{ event.venue_building }}
                    </span>
                </div>
                <div class="flex justify-between flex-col">
                    <span class="font-semibold">Levels:</span>
                    <span>{{ formatText(event.levels) }} </span>
                </div>
            </div>

            <div class="mt-4">
                <button
                    @click="openSingleEvent(event.id)"
                    class="w-full px-4 py-2 border border-gray-300 text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- /Event Detais -->

    <!-- Searched Results -->
    <div
        v-if="search_value != null"
        id="search-results-form"
        class="flex fixed inset-0 bg-gray-800 bg-opacity-50 justify-center items-center z-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold">
                    Search Results for
                    <span class="font-bold">"{{ search_value }}"</span>
                </h1>
                <button
                    onclick="document.getElementById('search-results-form').classList.toggle('hidden')"
                    class="text-2xl font-bold text-gray-600 hover:text-gray-900"
                >
                    &times;
                </button>
            </div>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-500 text-white">
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Event Name
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Date
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Date End
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Status
                        </th>
                        <th class="text-center font-medium px-4 py-2 border-b">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="result in searchResults"
                        :key="result.id"
                        class="text-center hover:bg-gray-200 cursor-pointer"
                    >
                        <td class="px-4 py-2">{{ result.name }}</td>
                        <td class="px-4 py-2">
                            {{ formatDate(result.date_start) }}
                        </td>
                        <td class="px-4 py-2">
                            {{ formatDate(result.date_end) }}
                        </td>

                        <td class="px-4 py-2 space-x-2 text-center">
                            <small>Venue Coordinator:</small>
                            <i
                                :class="
                                    result.isApprovedByVenueCoordinator != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                            <small>Admin:</small>
                            <i
                                :class="
                                    result.isApprovedByAdmin != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                        </td>

                        <td class="px-4 py-2">
                            <button
                                @click="
                                    openSingleSearchedEvent(result.event_id)
                                "
                                class="text-blue-500 hover:opacity-75"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-for="result in searchResults"
            :id="'preview-searched-' + result.event_id"
            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
        >
            <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-auto">
                <div class="mb-4 text-center">
                    <h1 class="text-xl font-semibold">Event Details</h1>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-semibold">Name:</span>
                        <span>{{ result.name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Department:</span>
                        <span>{{ result.department_acronyms }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Term:</span>
                        <span>{{ result.term_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Date Start:</span>
                        <span
                            >{{ formatDate(result.date_start) }} at
                            {{ formatTime(result.time_start) }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Date End:</span>
                        <span
                            >{{ formatDate(result.date_end) }} at
                            {{ formatTime(result.time_end) }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Venue:</span>
                        <span
                            >{{ result.venue_name }} at
                            {{ result.venue_building }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Venue:</span>
                        <span class="space-x-2">
                            <small>Venue Coordinator:</small>
                            <i
                                :class="
                                    result.isApprovedByVenueCoordinator != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                            <small>Admin:</small>
                            <i
                                :class="
                                    result.isApprovedByAdmin != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                        </span>
                    </div>
                </div>

                <button
                    @click="openSingleSearchedEvent(result.event_id)"
                    class="mt-4 w-full px-4 py-2 border border-gray-300 text-gray-800 rounded hover:bg-gray-100 text-center"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- /Searched Results -->

    <div class="flex flex-col">
        <div class="flex flex-col p-2 space-x-2">
            <div class="flex justify-between">
                <form
                    action="/admin/calendar-filter/"
                    method="GET"
                    enctype="multipart/form-data"
                    class="items-center flex space-x-2 p-2"
                >
                    <input
                        name="search_value"
                        placeholder="Search"
                        class="block p-2 border border-gray-300 rounded"
                    />
                    <input
                        type="hidden"
                        name="currentYear"
                        :value="currentYear"
                    />
                    <div>
                        <select
                            onchange="this.form.submit()"
                            name="department"
                            class="block p-2 border border-gray-300 w-full rounded"
                        >
                            <option
                                v-if="currentDepartment.id != 'all'"
                                :value="currentDepartment.id"
                            >
                                {{ currentDepartment.name }}
                            </option>
                            <option value="all">All</option>
                            <option
                                v-for="department in departments"
                                :key="department"
                                :value="department.id"
                            >
                                {{ department.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <select
                            onchange="this.form.submit()"
                            name="venue"
                            id=""
                            class="block p-2 border border-gray-300 w-full rounded"
                        >
                            <option
                                v-if="currentVenue.id != 'all'"
                                :value="currentVenue.id"
                            >
                                {{ currentVenue.name }} at
                                {{ currentVenue.building }}
                            </option>
                            <option value="all">All</option>
                            <option
                                v-for="venue in venues"
                                :value="venue.id"
                                :key="venue"
                            >
                                {{ venue.name }} at {{ venue.building }}
                            </option>
                        </select>
                    </div>
                </form>

                <div class="relative inline-block text-left">
                    <button
                        @click="toggleDropdown"
                        class="px-4 py-2 hover:opacity-50"
                    >
                        <i class="fas fa-ellipsis"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        v-if="isOpen"
                        class="absolute right-0 mt-2 w-[320px] bg-white shadow-md border rounded z-10"
                    >
                        <ul>
                            <li>
                                <a
                                    :href="
                                        '/admin/events-export-to-pdf/view/all/' +
                                        currentDepartment.id +
                                        '/' +
                                        currentYear
                                    "
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >View All Department's Record to .pdf</a
                                >
                            </li>
                            <li>
                                <a
                                    :href="
                                        '/admin/events-export-to-pdf/download/all/' +
                                        currentDepartment.id +
                                        '/' +
                                        currentYear
                                    "
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >Download All Department's Records to
                                    .pdf</a
                                >
                            </li>
                            <li v-if="currentDepartment.name != 'All'">
                                <a
                                    :href="
                                        '/admin/events-export-to-pdf/view/single/' +
                                        currentDepartment.id +
                                        '/' +
                                        currentYear
                                    "
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >View {{ currentDepartment.name }} to
                                    .pdf</a
                                >
                            </li>

                            <li v-if="currentDepartment.name != 'All'">
                                <a
                                    :href="
                                        '/admin/events-export-to-pdf/download/single/' +
                                        currentDepartment.id +
                                        '/' +
                                        currentYear
                                    "
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >Download {{ currentDepartment.name }} to
                                    .pdf</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="">
                <div
                    class="flex flex-col bg-gray-100 border-2 border-gray-300 rounded-xl"
                >
                    <div
                        class="w-full flex justify-between px-4 items-center border-b"
                    >
                        <button
                            @click="
                                selectedMonth != null
                                    ? selectedMonth >= 1
                                        ? changeMonth('prev')
                                        : changeMonthYear('prev')
                                    : changeYear('prev')
                            "
                            class=""
                        >
                            <i
                                class="fas fa-chevron-circle-left text-blue-500 fa-xl hover:opacity-50"
                            ></i>
                        </button>
                        <select
                            v-model="currentYear"
                            class="m-2 shadow-inner border focus:outline-none"
                            @change="updateCalendarDataForYear"
                        >
                            <option
                                v-for="year in Array.from(
                                    { length: 11 },
                                    (_, i) => i + 2020
                                )"
                                :key="year"
                                :value="year"
                            >
                                {{ year }}
                            </option>
                        </select>
                        <button
                            @click="
                                selectedMonth != null
                                    ? selectedMonth >= 0 && selectedMonth <= 10
                                        ? changeMonth('next')
                                        : changeMonthYear('next')
                                    : changeYear('next')
                            "
                            class=""
                        >
                            <i
                                class="fas fa-chevron-circle-right text-blue-500 fa-xl hover:opacity-50"
                            ></i>
                        </button>
                    </div>

                    <div
                        v-if="selectedMonth === null"
                        class="w-full rounded-lg overflow-y-auto h-[70vh]"
                    >
                        <div
                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 p-2"
                        >
                            <template
                                v-for="(month, monthIndex) in calendarData"
                                :key="monthIndex"
                                class=""
                            >
                                <div
                                    class="transition-transform duration-300 ease-in-out hover:scale-90 flex flex-col border border-gray-300 drop-shadow p-2 hover:bg-gray-200 cursor-pointer"
                                    @click="selectMonth(monthIndex)"
                                >
                                    <div
                                        class="bg-blue-300 font-bold text-center"
                                    >
                                        <span class="text-sm">
                                            {{
                                                [
                                                    "JANUARY",
                                                    "FEBRUARY",
                                                    "MARCH",
                                                    "APRIL",
                                                    "MAY",
                                                    "JUNE",
                                                    "JULY",
                                                    "AUGUST",
                                                    "SEPTEMBER",
                                                    "OCTOBER",
                                                    "NOVEMBER",
                                                    "DECEMBER",
                                                ][monthIndex]
                                            }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-7 p-1">
                                        <template
                                            v-for="dayOfWeek in daysOfWeek"
                                            :key="dayOfWeek"
                                        >
                                            <div
                                                :class="
                                                    ('flex justify-center items-center font-bold h-6 text-xs',
                                                    [
                                                        dayOfWeek == 'Sun'
                                                            ? 'text-red-500'
                                                            : '',
                                                    ])
                                                "
                                            >
                                                {{ dayOfWeek }}
                                            </div>
                                        </template>
                                        <template
                                            v-for="(day, index) in month"
                                            :key="day"
                                        >
                                            <!-- Line that "scratches" the day if there is a record -->

                                            <!-- Day Box -->
                                            <div
                                                v-if="day"
                                                :class="[
                                                    isHasRecord(
                                                        day,
                                                        monthIndex + 1,
                                                        currentYear,
                                                        events
                                                    )
                                                        ? 'bg-blue-500 text-blue-100'
                                                        : '',
                                                    'flex flex-col justify-center items-center h-6 text-xs relative',
                                                    isSunday(index)
                                                        ? 'text-red-500'
                                                        : '',
                                                    isToday(monthIndex + 1, day)
                                                        ? 'font-bold'
                                                        : '',
                                                ]"
                                            >
                                                {{ day }}

                                                <i
                                                    :class="[
                                                        'fixed mt-6 text-green-500 shadow-md',
                                                        {
                                                            'fas fa-circle text-[4px] font-bold':
                                                                isToday(
                                                                    monthIndex +
                                                                        1,
                                                                    day
                                                                ),
                                                        },
                                                    ]"
                                                ></i>
                                            </div>

                                            <div v-else class="h-6"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div v-else class="w-full h-full rounded-lg">
                        <div class="">
                            <div
                                class="bg-gradient-to-r from-blue-500 to-blue-800"
                            >
                                <div
                                    class="flex items-center justify-between px-2 space-x-2"
                                >
                                    <span
                                        class="text-blue-100 text-lg font-bold px-2"
                                        >{{
                                            [
                                                "JANUARY",
                                                "FEBRUARY",
                                                "MARCH",
                                                "APRIL",
                                                "MAY",
                                                "JUNE",
                                                "JULY",
                                                "AUGUST",
                                                "SEPTEMBER",
                                                "OCTOBER",
                                                "NOVEMBER",
                                                "DECEMBER",
                                            ][selectedMonth]
                                        }}
                                    </span>
                                    <button
                                        @click="backToYearView()"
                                        class="flex justify-center px-4 py-2 text-blue-100 hover:opacity-50 rounded"
                                    >
                                        back
                                    </button>
                                </div>
                            </div>
                            <div class="">
                                <div
                                    class="flex w-full justify-around font-bold p-2"
                                >
                                    <template
                                        v-for="dayOfWeek in daysOfWeek"
                                        :key="dayOfWeek"
                                        class=""
                                    >
                                        <div
                                            :class="'flex bg-gray-300 w-full justify-around'"
                                        >
                                            <span
                                                :class="
                                                    dayOfWeek == 'Sun'
                                                        ? 'text-red-500'
                                                        : ''
                                                "
                                                >{{ dayOfWeek }}
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <div class="grid grid-cols-7 h-[55vh]">
                                    <template
                                        v-for="(day, index) in calendarData[
                                            selectedMonth
                                        ]"
                                        :key="day"
                                        class="flex"
                                    >
                                        <button
                                            class="text-lg justify-center flex flex-col items-center text-xl"
                                            v-if="day"
                                            @click="
                                                [
                                                    isHasRecord(
                                                        day,
                                                        selectedMonth + 1,
                                                        currentYear,
                                                        events
                                                    )
                                                        ? eventsDetails(
                                                              day,
                                                              selectedMonth + 1,
                                                              currentYear,
                                                              eventsWithDetails
                                                          )
                                                        : checkDateNow(
                                                              currentYear +
                                                                  '-' +
                                                                  (selectedMonth +
                                                                      1) +
                                                                  '-' +
                                                                  day
                                                          )
                                                        ? ''
                                                        : user_role !=
                                                          'venue_coordinator'
                                                        ? openCreateEventModal(
                                                              day
                                                          )
                                                        : '',
                                                ]
                                            "
                                            :class="[
                                                isHasRecord(
                                                    day,
                                                    selectedMonth + 1,
                                                    currentYear,
                                                    events
                                                )
                                                    ? 'bg-blue-500 text-blue-100 hover:opacity-50'
                                                    : 'hover:bg-gray-200',
                                                isSunday(index)
                                                    ? 'text-red-500'
                                                    : '',
                                                {
                                                    'font-bold': isToday(
                                                        selectedMonth + 1,
                                                        day
                                                    ),
                                                },
                                            ]"
                                        >
                                            {{ day }}

                                            <i
                                                :class="{
                                                    'relative flex fixed justify-center text-green-500 fas fa-circle text-[8px]':
                                                        isToday(
                                                            selectedMonth + 1,
                                                            day
                                                        ),
                                                }"
                                            ></i>
                                        </button>

                                        <div v-else class=""></div>

                                        <div
                                            :id="'create-event-modal-' + day"
                                            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden"
                                        >
                                            <form
                                                @submit.prevent="submitForm"
                                                enctype="multipart/form-data"
                                                class="bg-white p-2 w-[500px] shadow-md rounded"
                                            >
                                                <div>
                                                    <input
                                                        type="date"
                                                        id="event_date_start"
                                                        class="hidden"
                                                        :value="
                                                            convertToDate(
                                                                selectedMonth +
                                                                    1,
                                                                day,
                                                                currentYear
                                                            )
                                                        "
                                                        name="event_date_start"
                                                    />

                                                    <input
                                                        type="hidden"
                                                        name="event_time_start"
                                                        :value="startTime"
                                                    />
                                                    <input
                                                        type="hidden"
                                                        name="event_time_end"
                                                        :value="endTime"
                                                    />
                                                </div>
                                                <div
                                                    class="flex w-full justify-between mb-4"
                                                >
                                                    <h1 class="text-2xl">
                                                        Create Event on
                                                        {{
                                                            [
                                                                "January",
                                                                "February",
                                                                "March",
                                                                "April",
                                                                "May",
                                                                "June",
                                                                "July",
                                                                "August",
                                                                "September",
                                                                "October",
                                                                "November",
                                                                "December",
                                                            ][selectedMonth]
                                                        }}
                                                        {{ day }},
                                                        {{ currentYear }}
                                                    </h1>

                                                    <button
                                                        type="button"
                                                        @click="
                                                            closeCreateEventModal(
                                                                day
                                                            )
                                                        "
                                                        class="text-2xl font-bold hover:opacity-50"
                                                    >
                                                        &times;
                                                    </button>
                                                </div>

                                                <div
                                                    class="flex items-center justify-between space-x-1"
                                                >
                                                    <div class="w-full">
                                                        <label for=""
                                                            >Venue:</label
                                                        >

                                                        <select
                                                            name="event_venue"
                                                            class="block p-2.5 border border-gray-300 w-full rounded"
                                                            required
                                                            @change="
                                                                onVenueChange(
                                                                    selectedDateForm,
                                                                    $event
                                                                        .target
                                                                        .value,
                                                                    eventsWithDetails
                                                                )
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                            ></option>
                                                            <option
                                                                v-for="venue in venues"
                                                                :key="venue"
                                                                :value="
                                                                    venue.id
                                                                "
                                                            >
                                                                {{ venue.name }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="w-full">
                                                        <label for=""
                                                            >Date End:</label
                                                        >
                                                        <input
                                                            type="date"
                                                            name="event_date_end"
                                                            id=""
                                                            v-model="
                                                                selectedDateStartForm
                                                            "
                                                            @change="
                                                                onVenueChange(
                                                                    $event
                                                                        .target
                                                                        .value,
                                                                    selectedVenue,
                                                                    eventsWithDetails
                                                                )
                                                            "
                                                            class="block p-2 border border-gray-300 w-full rounded"
                                                            required
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <div
                                                        class="flex justify-between w-full border rounded mt-1 border-gray-300 items-center space-x-2"
                                                    >
                                                        <div class="p-2">
                                                            <label
                                                                for="ampmStart"
                                                                class="block text-sm font-medium text-gray-700"
                                                                >Start
                                                                Time:</label
                                                            >
                                                            <div
                                                                :class="
                                                                    startTimeApproved
                                                                        ? 'border-2 border-green-500 shadow-md'
                                                                        : ''
                                                                "
                                                                class="flex p-1 rounded space-x-2"
                                                            >
                                                                <select
                                                                    id="ampmStart"
                                                                    name="ampmStart"
                                                                    v-model="
                                                                        selectedAMPMStart
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        disableTimePicker
                                                                    "
                                                                    @change="
                                                                        timeStartPeriodChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option>
                                                                        AM
                                                                    </option>
                                                                    <option>
                                                                        PM
                                                                    </option>
                                                                </select>
                                                                <select
                                                                    id="hourStart"
                                                                    name="hourStart"
                                                                    v-model="
                                                                        selectedHourStart
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        disableTimePicker
                                                                    "
                                                                    @change="
                                                                        timeStartHourChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option
                                                                        v-for="hour in hours"
                                                                        :key="
                                                                            hour
                                                                        "
                                                                        :disabled="
                                                                            isHourDisabled(
                                                                                hour,
                                                                                selectedAMPMStart
                                                                            )
                                                                        "
                                                                    >
                                                                        {{
                                                                            hour
                                                                        }}
                                                                    </option>
                                                                </select>

                                                                <select
                                                                    id="minuteStart"
                                                                    name="minuteStart"
                                                                    v-model="
                                                                        selectedMinuteStart
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        disableTimePicker
                                                                    "
                                                                    @change="
                                                                        timeStartMinutesChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option
                                                                        v-for="minute in minutes"
                                                                        :key="
                                                                            minute
                                                                        "
                                                                    >
                                                                        {{
                                                                            minute
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- End Time Picker -->
                                                        <div class="p-2">
                                                            <label
                                                                for="ampmEnd"
                                                                class="block text-sm font-medium text-gray-700"
                                                                >End
                                                                Time:</label
                                                            >
                                                            <div
                                                                :class="
                                                                    endTimeApproved
                                                                        ? 'border-2 border-green-500 shadow-md'
                                                                        : ''
                                                                "
                                                                class="flex p-1 rounded space-x-2"
                                                            >
                                                                <select
                                                                    id="ampmEnd"
                                                                    name="ampmEnd"
                                                                    v-model="
                                                                        selectedAMPMEnd
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        startTimeDisable
                                                                    "
                                                                    @change="
                                                                        timeEndPeriodChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option></option>
                                                                    <option>
                                                                        AM
                                                                    </option>
                                                                    <option>
                                                                        PM
                                                                    </option>
                                                                </select>
                                                                <select
                                                                    id="hourEnd"
                                                                    name="hourEnd"
                                                                    v-model="
                                                                        selectedHourEnd
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        startTimeDisable
                                                                    "
                                                                    @change="
                                                                        timeEndHourChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option
                                                                        v-for="hour in hours"
                                                                        :key="
                                                                            hour
                                                                        "
                                                                        :disabled="
                                                                            isHourDisabled(
                                                                                hour,
                                                                                selectedAMPMEnd
                                                                            )
                                                                        "
                                                                        required
                                                                    >
                                                                        {{
                                                                            hour
                                                                        }}
                                                                    </option>
                                                                </select>
                                                                <select
                                                                    id="minuteEnd"
                                                                    name="minuteEnd"
                                                                    v-model="
                                                                        selectedMinuteEnd
                                                                    "
                                                                    class="border p-2 rounded focus:outline-none"
                                                                    :disabled="
                                                                        startTimeDisable
                                                                    "
                                                                    @change="
                                                                        timeEndMinutesChange(
                                                                            $event
                                                                                .target
                                                                                .value
                                                                        )
                                                                    "
                                                                    required
                                                                >
                                                                    <option
                                                                        v-for="minute in minutes"
                                                                        :key="
                                                                            minute
                                                                        "
                                                                    >
                                                                        {{
                                                                            minute
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for=""
                                                        >Event Title:</label
                                                    >
                                                    <input
                                                        type="text"
                                                        name="event_name"
                                                        id=""
                                                        placeholder="Input event title"
                                                        class="w-full block p-2 border border-gray-300 rounded shadow-inner"
                                                        required
                                                    />
                                                </div>
                                                <div class="flex space-x-2">
                                                    <div class="h-full">
                                                        <label for=""
                                                            >Departments:</label
                                                        >

                                                        <select
                                                            name="event_departments[]"
                                                            class="block p-2 border border-gray-300 w-full rounded"
                                                            required
                                                            :id="
                                                                'department-' +
                                                                day
                                                            "
                                                            @change="
                                                                onDepartmentChange(
                                                                    day
                                                                )
                                                            "
                                                            multiple
                                                            :size="
                                                                departments.length
                                                            "
                                                        >
                                                            <option
                                                                v-for="department in departments"
                                                                :key="
                                                                    department.accronym
                                                                "
                                                                :value="
                                                                    department.accronym
                                                                "
                                                                :selected="
                                                                    isSelected(
                                                                        department.accronym
                                                                    )
                                                                "
                                                            >
                                                                {{
                                                                    department.name +
                                                                    "(" +
                                                                    department.accronym +
                                                                    ")"
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <div>
                                                            <label for=""
                                                                >Term:</label
                                                            >
                                                            <select
                                                                name="event_term_id"
                                                                id=""
                                                                class="w-full block p-2 border border-gray-300 rounded shadow-inner"
                                                                required
                                                            >
                                                                <option
                                                                    v-for="term in terms"
                                                                    :key="term"
                                                                    :value="
                                                                        term.id
                                                                    "
                                                                >
                                                                    {{
                                                                        term.name
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label
                                                                for="event_levels"
                                                                >Levels</label
                                                            >
                                                            <select
                                                                name="event_levels[]"
                                                                class="block p-2 border w-full border-gray-300 rounded shadow-inner"
                                                                required
                                                                multiple
                                                            >
                                                                <option
                                                                    value="All Levels"
                                                                >
                                                                    All Levels
                                                                </option>
                                                                <option
                                                                    v-for="(
                                                                        item,
                                                                        index
                                                                    ) in level_lists"
                                                                    :key="index"
                                                                    :value="
                                                                        item.value
                                                                    "
                                                                >
                                                                    {{
                                                                        item.label
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for=""
                                                                >Activity Design
                                                                (PDF file
                                                                only):</label
                                                            >
                                                            <input
                                                                type="file"
                                                                name="activity_design"
                                                                class="block p-2 border border-gray-300 w-full rounded"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex justify-between"
                                                ></div>

                                                <div
                                                    class="flex justify-end p-2"
                                                >
                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                                    >
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="success"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white p-2">
            {{ success }}
        </div>
    </div>
</template>
<script>
import { ref } from "vue";

const hours = [
    "12",
    "01",
    "02",
    "03",
    "04",
    "05",
    "06",
    "07",
    "08",
    "09",
    "10",
    "11",
];
const minutes = Array.from({ length: 60 }, (_, i) =>
    i.toString().padStart(2, "0")
);

export default {
    data() {
        return {
            isOpen: false,
            dateSelected: "",
            daySelected: "",
            filteredEvents: [],
            success: "",
            level_lists: [],
            selectedDepartment: "",
            selectedVenue: "",
            errors: "",
            pageTitle: "",
            user: [],
            selectedDepartments: [],
            unavailableTimes: [],
            isDisabled: false,
            disableTimePicker: true,
            endPeriod: "",
            endHour: "",
            endMinutes: "",
            startPeriod: "",
            startHour: "",
            startMinutes: "",

            startTime: null, // Combined start time
            endTime: null, // Combined end time

            selectedAMPMStart: null,
            selectedAMPMEnd: null,
            selectedHourStart: null,
            selectedMinuteStart: null,
            selectedHourEnd: null,
            selectedMinuteEnd: null,

            selectedDateForm: "",
            selectedDateStartForm: "",

            startTimeApproved: false,
            endTimeApproved: false,
            startTimeDisable: true,
        };
    },
    inheritAttrs: false,
    props: {
        search_value: String,
        currentDepartment: Object,
        departments: Object,
        venues: Object,
        events: {
            type: Array,
            required: true,
        },
        terms: Object,
        user_role_calendar: String,
        successMessage: String,
        errorMessage: String,
        eventsWithDetails: Array,
        user_role: String,
        searchResults: Array,
        selectedDate: {},
        currentVenue: {},
    },

    methods: {
        timeEndPeriodChange(value) {
            this.endPeriod = value;
        },
        timeEndHourChange(value) {
            this.endHour = value;
        },
        timeEndMinutesChange(value) {
            this.endMinutes = value;
        },
        timeStartPeriodChange(value) {
            this.startPeriod = value;
        },
        timeStartHourChange(value) {
            this.startHour = value;
        },
        timeStartMinutesChange(value) {
            this.startMinutes = value;
        },

        onVenueChange(date, venueId, events) {
            let date_end = date;
            let date_start = this.selectedDateForm;

            // Function to format Date object to 'YYYY-MM-DD'
            const formatDate = (dateObj) => {
                const year = dateObj.getFullYear();
                const month = dateObj.getMonth() + 1;
                const day = dateObj.getDate();
                return `${year}-${month.toString().padStart(2, "0")}-${day
                    .toString()
                    .padStart(2, "0")}`;
            };

            // Format date_end if it's a Date object
            if (typeof date === "object" && date instanceof Date) {
                date_end = formatDate(date);
            }

            // Format date_start (since it's always an object)
            if (typeof date_start === "object" && date_start instanceof Date) {
                date_start = formatDate(date_start);
            }

            const filteredEvents = events.filter((event) => {
                // Check if the event venue matches the provided venueId
                if (
                    event.venue_id === parseInt(venueId) &&
                    event.isApprovedByAdmin !== null
                ) {
                    const eventStartDate = new Date(
                        event.date_start + "T00:00:00"
                    );
                    const eventEndDate = new Date(event.date_end + "T23:59:59");
                    const filterStartDate = new Date(date_start + "T00:00:00");
                    const filterEndDate = new Date(date_end + "T23:59:59");

                    const isInRange =
                        (eventStartDate >= filterStartDate &&
                            eventStartDate <= filterEndDate) ||
                        (eventEndDate >= filterStartDate &&
                            eventEndDate <= filterEndDate) ||
                        (eventStartDate <= filterStartDate &&
                            eventEndDate >= filterEndDate);

                    return isInRange;
                }
                return false;
            });

            this.disableTimePicker = false;
            this.selectedVenue = venueId;

            this.unavailableTimes = filteredEvents
                .filter((event) => event.time_start && event.time_end)
                .map((event) => {
                    const [startHour, startMinute] = event.time_start
                        .split(":")
                        .slice(0, 2);
                    const [endHour, endMinute] = event.time_end
                        .split(":")
                        .slice(0, 2);
                    const startPeriod = parseInt(startHour) >= 12 ? "PM" : "AM";
                    const endPeriod = parseInt(endHour) >= 12 ? "PM" : "AM";
                    const start = `${String(
                        parseInt(startHour) % 12 || 12
                    ).padStart(2, "0")}:${startMinute.padStart(
                        2,
                        "0"
                    )} ${startPeriod}`;
                    const end = `${String(
                        parseInt(endHour) % 12 || 12
                    ).padStart(2, "0")}:${endMinute.padStart(
                        2,
                        "0"
                    )} ${endPeriod}`;
                    return { start, end };
                });

            console.log(this.unavailableTimes);
        },
        convertTimeToMinutes(time) {
            const [hours, minutes] = time.split(/[: ]/).slice(0, 2).map(Number);
            const period = time.split(" ")[1];
            return (
                (period === "PM" && hours !== 12 ? hours + 12 : hours) * 60 +
                minutes
            );
        },
        convertMinutesToTime(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            const period = hours >= 12 ? "PM" : "AM";
            const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
            return `${String(formattedHours).padStart(2, "0")}:${String(
                mins
            ).padStart(2, "0")} ${period}`;
        },
        convertTimeToHour(time) {
            const [hour, minute] = time.split(":");
            const [minutePart, period] = minute.split(" ");

            let hour24 = parseInt(hour, 10);
            if (period === "PM" && hour24 !== 12) {
                hour24 += 12; // Convert PM to 24-hour format (except for 12 PM)
            } else if (period === "AM" && hour24 === 12) {
                hour24 = 0; // Convert 12 AM to 0 in 24-hour format
            }
            return hour24;
        },
        isHourDisabled(hour, selectedTimePeriod) {
            const disabledHours = this.unavailableTimes.flatMap((range) => {
                const startHour = this.convertTimeToHour(range.start);
                const endHour = this.convertTimeToHour(range.end);
                let hoursInRange = [];

                // Case 1: Start is AM and End is AM - Disable hours between start and end in the AM range
                if (startHour < 12 && endHour < 12) {
                    for (let h = startHour; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                }
                // Case 2: Start is AM and End is PM - Disable all hours from start to 12 PM
                else if (startHour < 12 && endHour >= 12) {
                    // Disable hours from the start to 12 PM (AM)
                    for (let h = startHour; h < 12; h++) {
                        hoursInRange.push(h % 24);
                    }
                    // Disable all hours from 12 PM to the end hour
                    for (let h = 12; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                }
                // Case 3: Start is PM and End is PM - Disable hours between start and end in the PM range
                else if (startHour >= 12 && endHour >= 12) {
                    for (let h = startHour; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                }

                return hoursInRange;
            });

            const hourInt = parseInt(hour);
            let hour24 = hourInt;

            // Convert to 24-hour format if PM
            if (selectedTimePeriod === "PM" && hourInt !== 12) {
                hour24 = hourInt + 12;
            }

            // Return if the hour is in the disabled range
            return disabledHours.includes(hour24);
        },
        updateStartTime() {
            let hour = this.startHour;
            let minutes = this.startMinutes;
            let period = this.startPeriod ? this.startPeriod.toUpperCase() : "";

            if (period === "PM" && hour < 12) hour = parseInt(hour) + 12; // Convert PM hour
            if (period === "AM" && hour == 12) hour = 0; // Convert 12 AM to 00

            const formattedStartTime = new Date();
            formattedStartTime.setHours(hour);
            formattedStartTime.setMinutes(minutes);

            // Store the start time in the component
            this.startTime = formattedStartTime;

            // Call watchTimeChange to check for overlap
            this.watchTimeChange();
        },

        // Method to update end time from the separate components
        updateEndTime() {
            let hour = this.endHour;
            let minutes = this.endMinutes;
            let period = this.endPeriod ? this.endPeriod.toUpperCase() : ""; // AM/PM should be uppercase

            if (period === "PM" && hour < 12) hour = parseInt(hour) + 12; // Convert PM hour
            if (period === "AM" && hour == 12) hour = 0; // Convert 12 AM to 00

            const formattedEndTime = new Date();
            formattedEndTime.setHours(hour);
            formattedEndTime.setMinutes(minutes);

            // Store the end time in the component
            this.endTime = formattedEndTime;

            // Call watchTimeChange to check for overlap
            this.watchTimeChange();
        },

        // Function to check if the time ranges overlap
        isTimeOverlapping(startTime, endTime, rangeStart, rangeEnd) {
            // Check if the selected range overlaps with the existing range
            return startTime < rangeEnd && endTime > rangeStart; // Adjust comparison logic as needed
        },
        formatSelectedTime(date) {
            return date.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: true,
            });
        },

        convertTimeToMinutes(timeStr) {
            const [time, modifier] = timeStr.split(" ");
            let [hours, minutes] = time.split(":").map(Number);

            if (modifier === "PM" && hours !== 12) hours += 12;
            if (modifier === "AM" && hours === 12) hours = 0;

            return hours * 60 + minutes; // Convert to minutes
        },
        rangesOverlap(range1, range2) {
            const range1Start = this.convertTimeToMinutes(range1.start);
            const range1End = this.convertTimeToMinutes(range1.end);
            const range2Start = this.convertTimeToMinutes(range2.start);
            const range2End = this.convertTimeToMinutes(range2.end);

            // Check for overlap
            return range1Start < range2End && range1End > range2Start;
        },

        // Function to handle time change and check for overlaps
        watchTimeChange() {
            let date = this.startTime; // Date object

            // Extract the hour and minute
            let hour = date.getHours(); // 4 (for 4 AM/PM)
            let minutes = date.getMinutes(); // 6 (for 06 minutes)

            // Determine AM/PM period
            let period = hour >= 12 ? "PM" : "AM";

            // Convert hour to 12-hour format
            let formattedHour = hour % 12;
            if (formattedHour === 0) formattedHour = 12; // Handle 0 hour as 12

            // Ensure hour, minutes, and period are not null or undefined
            if (formattedHour != null && minutes != null && period != null) {
                // Convert to string if needed (for example "8:30 PM")
                let timeString = `${formattedHour}:${
                    minutes < 10 ? "0" + minutes : minutes
                } ${period}`;

                console.log(`Time String: ${timeString}`); // Logs the formatted time

                this.startTimeApproved = true;
                this.startTimeDisable = false;
            } else {
                console.error("Invalid time components");
            }

            // Ensure that startTime and endTime are valid Date objects
            if (this.startTime && this.endTime) {
                // Convert start and end times to milliseconds
                const startTime = this.startTime.getTime();
                const endTime = this.endTime.getTime();

                // Check if endTime is greater than startTime
                if (endTime <= startTime) {
                    alert("End time must be greater than start time.");
                    this.selectedHourEnd = null;
                    this.selectedMinuteEnd = null;
                    this.endTime = null;
                    this.endTimeApproved = false;
                    return;
                }

                // Format the start and end times to 'hh:mm AM/PM'
                const formattedStartTime = this.formatSelectedTime(
                    this.startTime
                );
                const formattedEndTime = this.formatSelectedTime(this.endTime);

                const formattedTimes = {
                    start: formattedStartTime,
                    end: formattedEndTime,
                };

                let isOverlapping = false;
                // Loop through existing ranges to check for overlap
                for (let existingRange of this.unavailableTimes) {
                    if (this.rangesOverlap(formattedTimes, existingRange)) {
                        isOverlapping = true;
                        break;
                    }
                }

                if (isOverlapping) {
                    this.selectedHourStart = null;
                    this.selectedMinuteStart = null;
                    this.selectedHourEnd = null;
                    this.selectedMinuteEnd = null;
                    this.startTime = null;
                    this.endTime = null;
                    this.startTimeApproved = false;
                    this.endTimeApproved = false;
                    alert(
                        "This time is not available! Please try another time."
                    );
                } else {
                    this.startTimeApproved = true;
                    this.endTimeApproved = true;
                }
            }
        },
        formatText(text) {
            return text.replace(/[\[\]\""]/g, "").replace(/,/g, ", ");
        },

        isSelected(departmentAcronym) {
            return this.selectedDepartments.includes(departmentAcronym);
        },

        departmentColor(deptIds) {
            let deptIdString = deptIds.replace(/[\[\]\""\,\s+]/g, "");

            let deptIdArray = deptIdString.split("").map(Number);
            let deptIdSum = deptIdArray.reduce((acc, num) => acc + num, 0);

            let hash = deptIdSum * 1234567;
            hash = (hash + deptIdSum) * 9876543;

            let hex =
                "#" + ((hash & 0xffffff) + 0x1000000).toString(16).slice(1);
            hex += "30";

            return hex;
        },
        checkDateNow(date) {
            const selectedDate = new Date(date);

            const dateToday = new Date();

            dateToday.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate >= dateToday) {
                return false;
            } else {
                return true;
            }
        },

        onDepartmentChange(day) {
            const selectedDepts = Array.from(
                document.getElementById("department-" + day).selectedOptions
            ).map((option) => option.value);

            const departmentLevels = {
                College: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                COE: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                CAST: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                CCJ: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                CON: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                CABM: [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                "CABM-B": [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                "CABM-H": [
                    { label: "4th yrs", value: "4th yrs" },
                    { label: "3rd yrs", value: "3rd yrs" },
                    { label: "2nd yrs", value: "2nd yrs" },
                    { label: "1st yrs", value: "1st yrs" },
                ],
                ELEM: [
                    { label: "6th grade", value: "6th grade" },
                    { label: "5th grade", value: "5th grade" },
                    { label: "4th grade", value: "4th grade" },
                    { label: "3rd grade", value: "3rd grade" },
                    { label: "2nd grade", value: "2nd grade" },
                    { label: "1st grade", value: "1st grade" },
                ],
                JHS: [
                    { label: "10th grade", value: "10th grade" },
                    { label: "9th grade", value: "9th grade" },
                    { label: "8th grade", value: "8th grade" },
                    { label: "7th grade", value: "7th grade" },
                ],
                HS: [
                    { label: "K-11", value: "K-11" },
                    { label: "K-12", value: "K-12" },
                    { label: "10th grade", value: "10th grade" },
                    { label: "9th grade", value: "9th grade" },
                    { label: "8th grade", value: "8th grade" },
                    { label: "7th grade", value: "7th grade" },
                ],
                GS: [{ label: "None", value: "None" }],
                SHS: [
                    { label: "K-11", value: "K-11" },
                    { label: "K-12", value: "K-12" },
                ],
                "PRE-K": [
                    { label: "Kinder 1", value: "Kinder 1" },
                    { label: "Kinder 2", value: "Kinder 2" },
                ],
            };

            const combinedLevelSets = new Set();

            selectedDepts.forEach((selectedDept) => {
                if (departmentLevels[selectedDept]) {
                    departmentLevels[selectedDept].forEach((level) => {
                        // Use JSON stringification to ensure deep comparison (by value)
                        combinedLevelSets.add(JSON.stringify(level));
                    });
                }
            });

            // Convert the Set back to an array of objects
            this.level_lists = Array.from(combinedLevelSets).map((levelStr) =>
                JSON.parse(levelStr)
            );
        },
        selectedDateToPush(selectedDate, eventsWithDetails) {
            if (selectedDate != null) {
                const date = new Date(selectedDate);

                const day = date.getDate();
                const month = date.getMonth() + 1;
                const year = date.getFullYear();

                this.eventsDetails(
                    day,
                    selectedMonth,
                    currentYear,
                    eventsWithDetails
                );
            }
        },
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
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        closeDropdown() {
            this.isOpen = false;
        },
        submitForm(event) {
            const formData = new FormData(event.target);

            Inertia.post("/admin/event-create", formData, {
                preserveState: true,
            });
        },
        isSunday(index) {
            return index % 7 === 0;
        },
        eventsDetails(day, selectedMonth, currentYear, eventsWithDetails) {
            if (!eventsWithDetails || eventsWithDetails.length === 0) {
                this.filteredEvents = [];
                return;
            }

            const date = new Date(currentYear, selectedMonth - 1, day + 1); // Adjust for selected day
            const dateText = new Date(currentYear, selectedMonth - 1, day); // For displaying selected date

            const formatDate = (dateObj) => {
                const year = dateObj.getFullYear();
                const month = dateObj.getMonth() + 1;
                const day = dateObj.getDate();
                return `${year}-${month.toString().padStart(2, "0")}-${day
                    .toString()
                    .padStart(2, "0")}`;
            };

            this.selectedDateForm = dateText;
            this.selectedDateStartForm = formatDate(dateText);
            // Format the dates for display purposes
            const formattedDate = date.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            });

            const formattedDateNew = dateText.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            });

            this.dateSelected = formattedDateNew;
            this.daySelected = day;

            const formattedInputDate = date.toISOString().split("T")[0]; // ISO string format of selected date (YYYY-MM-DD)

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
        openCreateEventModal(day) {
            document
                .getElementById("create-event-modal-" + day)
                .classList.remove("hidden");
        },

        closeCreateEventModal(day) {
            document
                .getElementById("create-event-modal-" + day)
                .classList.add("hidden");
        },
        mounted() {
            this.selectedDateToPush(selectedDate, eventsWithDetails);
        },
    },
    watch: {
        // Watch for changes in start time components
        startPeriod(newValue, oldValue) {
            this.updateStartTime();
        },
        startHour(newValue, oldValue) {
            this.updateStartTime();
        },
        startMinutes(newValue, oldValue) {
            this.updateStartTime();
        },

        endPeriod(newValue, oldValue) {
            this.updateEndTime();
        },
        endHour(newValue, oldValue) {
            this.updateEndTime();
        },
        endMinutes(newValue, oldValue) {
            this.updateEndTime();
        },
    },
};
</script>
