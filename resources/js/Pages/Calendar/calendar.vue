<script setup>
import { ref } from "vue";
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
    if (direction === "previous" && currentYear.value > 2020) {
        currentYear.value--;
    } else if (direction === "next" && currentYear.value < 2030) {
        currentYear.value++;
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

    const dateString = date.toISOString().split("T")[0];

    return validEvents.some((event) => {
        const eventDateString = new Date(event).toISOString().split("T")[0];
        return eventDateString === dateString;
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
                <table class="w-[600px] border-collapse">
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
                                Time Start
                            </th>
                            <th
                                class="text-center font-medium text-gray-700 border-b"
                            >
                                Time End
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
                            <td>
                                {{ formatTime(event.time_start) }}
                            </td>
                            <td>
                                {{ formatTime(event.time_end) }}
                            </td>
                            <td>
                                <button
                                    @click="openSingleEvent(event.id)"
                                    class="hover:opacity-50"
                                >
                                    <i class="fas fa-eye text-blue-500"></i>
                                </button>
                            </td>
                            <div
                                :id="'preview-' + event.id"
                                class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
                            >
                                <div class="bg-white rounded p-2">
                                    <div>
                                        <h1 class="text-xl font-semibold">
                                            Event Details
                                        </h1>
                                    </div>

                                    <div class="flex flex-col items-start">
                                        <span
                                            ><strong>Name:</strong>
                                            {{ event.name }}</span
                                        >

                                        <span
                                            ><strong>Department:</strong>
                                            {{ event.department_name }}
                                        </span>
                                        <span
                                            ><strong>Term:</strong>
                                            {{ event.term_name }}
                                        </span>
                                        <span>
                                            <strong>Date Start:</strong>

                                            {{ formatDate(event.date_start) }}
                                            <span>at</span>
                                            {{ formatTime(event.time_start) }}
                                        </span>
                                        <span
                                            ><strong>Date End:</strong>
                                            {{ formatDate(event.date_end) }}
                                            <span>at</span>
                                            {{ formatTime(event.time_end) }}
                                        </span>
                                        <span
                                            ><strong>Venue:</strong>
                                            {{ event.venue_name }} at
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
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-end p-2">
                    <button
                        v-if="user_role != 'venue_coordinator'"
                        @click="openCreateEventModal(daySelected)"
                        class="px-4 py-2 bg-blue-500 text-blue-100 rounded hover:opacity-50"
                    >
                        Add Event
                    </button>
                </div>
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
        <div class="bg-white rounded shadow-md">
            <div class="flex justify-between items-center px-2">
                <h1 class="text-xl">
                    Search Results of <span>"{{ search_value }}"</span>
                </h1>
                <button
                    onclick="document.getElementById('search-results-form').classList.toggle('hidden')"
                    class="text-2xl font-bold hover:opacity-50"
                >
                    &times;
                </button>
            </div>
            <table class="w-[500px] border-collapse">
                <thead>
                    <tr class="w-full bg-gray-500 text-white">
                        <th class="text-center font-medium border-b">
                            Event Name
                        </th>
                        <th class="text-center font-medium border-b">Date</th>
                        <th class="text-center font-medium border-b">
                            Date End
                        </th>
                        <th class="text-center font-medium border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="result in searchResults"
                        :key="result.id"
                        class="text-center hover:bg-gray-200 cursor-pointer"
                    >
                        <td>{{ result.name }}</td>
                        <td>{{ formatDate(result.date_start) }}</td>
                        <td>{{ formatDate(result.date_end) }}</td>
                        <td>
                            <button
                                @click="
                                    openSingleSearchedEvent(result.event_id)
                                "
                                class="hover:opacity-50"
                            >
                                <i class="fas fa-eye text-blue-500"></i>
                            </button>
                        </td>

                        <div
                            :id="'preview-searched-' + result.event_id"
                            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
                        >
                            <div class="bg-white rounded p-2">
                                <div>
                                    <h1 class="text-xl font-semibold">
                                        Event Details
                                    </h1>
                                </div>

                                <div class="flex flex-col items-start">
                                    <span
                                        ><strong>Name:</strong>
                                        {{ result.name }}</span
                                    >

                                    <span
                                        ><strong>Department:</strong>
                                        {{ result.department_name }}
                                    </span>
                                    <span
                                        ><strong>Term:</strong>
                                        {{ result.term_name }}
                                    </span>
                                    <span>
                                        <strong>Date Start:</strong>
                                        {{ formatDate(result.date_start) }}
                                        <span>at</span>
                                        {{ formatTime(result.time_start) }}
                                    </span>

                                    <span
                                        ><strong>Date End:</strong>
                                        {{ formatDate(result.date_end) }}
                                        <span>at</span>
                                        {{ formatTime(result.time_end) }}
                                    </span>
                                    <span
                                        ><strong>Venue:</strong>
                                        {{ result.venue_name }} at
                                        {{ result.venue_building }}
                                    </span>
                                </div>

                                <button
                                    @click="
                                        openSingleSearchedEvent(result.event_id)
                                    "
                                    class="mt-2 px-4 py-2 border border-gray-300 text-gray-800 rounded hover:opacity-50"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </tr>
                </tbody>
            </table>
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
                            <option :value="currentDepartment.id">
                                {{ currentDepartment.name }}
                            </option>
                            <option
                                v-for="department in departments"
                                :key="department"
                                :value="department.id"
                            >
                                {{ department.name }}
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
                            <li>
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

                            <li>
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
                        <button @click="changeYear('previous')" class="">
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
                        <button @click="changeYear('next')" class="">
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
                                    <div class="grid grid-cols-7 gap-1 p-1">
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
                                            <div
                                                v-if="day"
                                                :class="[
                                                    isHasRecord(
                                                        day + 1,
                                                        monthIndex + 1,
                                                        currentYear,
                                                        events
                                                    )
                                                        ? 'text-blue-100 bg-blue-500'
                                                        : '',

                                                    'flex justify-center items-center h-6 text-xs',

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
                                                        'flex fixed bottom-3 text-green-500',
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
                                                >{{ dayOfWeek }}</span
                                            >
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
                                                        day + 1,
                                                        selectedMonth + 1,
                                                        currentYear,
                                                        events
                                                    )
                                                        ? eventsDetails(
                                                              day + 1,
                                                              selectedMonth + 1,
                                                              currentYear,
                                                              eventsWithDetails
                                                          )
                                                        : openCreateEventModal(
                                                              day
                                                          ),
                                                ]
                                            "
                                            :disabled="
                                                user_role_calendar ===
                                                'venue_coordinator'
                                            "
                                            :class="[
                                                isHasRecord(
                                                    day + 1,
                                                    selectedMonth + 1,
                                                    currentYear,
                                                    events
                                                )
                                                    ? 'hover:opacity-50 bg-blue-500 text-blue-100'
                                                    : 'hover:bg-gray-200',
                                                isSunday(index)
                                                    ? 'text-red-500'
                                                    : '',
                                                '',
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
                                                </div>
                                                <div>
                                                    <label for=""
                                                        >Date End:</label
                                                    >
                                                    <input
                                                        type="date"
                                                        name="event_date_end"
                                                        id=""
                                                        :value="
                                                            convertToDate(
                                                                selectedMonth +
                                                                    1,
                                                                day,
                                                                currentYear
                                                            )
                                                        "
                                                        class="w-full block p-2 border border-gray-300 rounded shadow-inner"
                                                        required
                                                    />
                                                </div>
                                                <div>
                                                    <label for="">Time:</label>
                                                    <div
                                                        class="flex items-center space-x-2"
                                                    >
                                                        <input
                                                            type="time"
                                                            name="event_time_start"
                                                            id=""
                                                            class="block p-2 border border-gray-300 w-full rounded"
                                                            required
                                                        />
                                                        <h1>To</h1>
                                                        <input
                                                            type="time"
                                                            name="event_time_end"
                                                            id=""
                                                            class="block p-2 border border-gray-300 w-full rounded"
                                                        />
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
                                                    <div>
                                                        <label for=""
                                                            >Departments:</label
                                                        >

                                                        <select
                                                            name="event_department_id"
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
                                                        >
                                                            <option
                                                                value=""
                                                            ></option>
                                                            <option
                                                                v-for="department in departments"
                                                                :key="
                                                                    department.accronym
                                                                "
                                                                :value="
                                                                    department.accronym
                                                                "
                                                            >
                                                                {{
                                                                    department.name
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
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
                                                                :value="term.id"
                                                            >
                                                                {{ term.name }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label for="">Venue:</label>
                                                    <select
                                                        name="event_venue"
                                                        class="block p-2 border border-gray-300 w-full rounded"
                                                        required
                                                    >
                                                        <option
                                                            v-for="venue in venues"
                                                            :key="venue"
                                                            :value="venue.id"
                                                        >
                                                            {{ venue.name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="event_levels"
                                                        >Levels</label
                                                    >
                                                    <select
                                                        name="event_levels"
                                                        class="block p-2 border border-gray-300 rounded shadow-inner"
                                                        required
                                                    >
                                                        <option
                                                            v-for="(
                                                                item, index
                                                            ) in level_lists"
                                                            :key="index"
                                                            :value="item.value"
                                                        >
                                                            {{ item.label }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for=""
                                                        >Activity Design:</label
                                                    >
                                                    <input
                                                        type="file"
                                                        name="activity_design"
                                                        class="block p-2 border border-gray-300 w-full rounded"
                                                    />
                                                </div>

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
const convertToDate = (month, day, year) => {
    const date = new Date(year, month - 1, day);
    const formattedYear = date.getFullYear();
    const formattedMonth = String(date.getMonth() + 1).padStart(2, "0");
    const formattedDay = String(date.getDate()).padStart(2, "0");

    return `${formattedYear}-${formattedMonth}-${formattedDay}`;
};

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
            errors: "",
            pageTitle: "",
            user: [],
        };
    },
    props: {
        search_value: {
            type: String,
        },
        currentDepartment: {
            type: Object,
        },
        departments: {
            type: Object,
        },
        venues: {
            type: Object,
        },
        events: {
            type: Array,
            required: true,
        },
        terms: {
            type: Object,
        },
        user_role_calendar: {
            type: String,
        },
        successMessage: {
            type: String,
        },
        errorMessage: {
            type: String,
        },
        eventsWithDetails: {
            type: Array,
        },
        user_role: {
            type: String,
        },
        searchResults: {
            type: Array,
        },
        selectedDate: {},
    },
    methods: {
        onDepartmentChange(day) {
            const selectedDept = document.getElementById(
                "department-" + day
            ).value;
            if (
                [
                    "College",
                    "COE",
                    "CABM",
                    "CABM-B",
                    "CABM-H",
                    "CAST",
                    "CCJ",
                    "COE",
                    "CON",
                ].includes(selectedDept)
            ) {
                let level_lists = [
                    { label: "All", value: "[1,2,3,4]" },
                    { label: "1-3rd yrs", value: "[1,2,3]" },
                    { label: "1-2nd yrs", value: "[1,2]" },
                    { label: "2-4th yrs", value: "[2,3,4]" },
                    { label: "2-3rd yrs", value: "[2,3]" },
                    { label: "3-4th yrs", value: "[3,4]" },
                    { label: "4th yrs", value: "[4]" },
                    { label: "3rd yrs", value: "[3]" },
                    { label: "2nd yrs", value: "[2]" },
                    { label: "1st yrs", value: "[1]" },
                ];

                this.level_lists = level_lists;
            } else if (selectedDept == "ELEM") {
                let level_lists = [
                    { label: "All", value: "[1,2,3,4,5,6]" },
                    { label: "1-5th grade", value: "[1,2,3,4,5]" },
                    { label: "1-4th grade", value: "[1,2,3,4]" },
                    { label: "1-3rd grade", value: "[1,2,3]" },
                    { label: "1-2nd grade", value: "[1,2]" },
                    { label: "2-6th grade", value: "[2,3,4,5,6]" },
                    { label: "2-5th grade", value: "[2,3,4,5]" },
                    { label: "2-4th grade", value: "[2,3,4]" },
                    { label: "2-3rd grade", value: "[2,3]" },
                    { label: "3-4th grade", value: "[3,4]" },
                    { label: "6th grade", value: "[6]" },
                    { label: "5th grade", value: "[5]" },
                    { label: "4th grade", value: "[4]" },
                    { label: "3rd grade", value: "[3]" },
                    { label: "2nd grade", value: "[2]" },
                    { label: "1st grade", value: "[1]" },
                ];

                this.level_lists = level_lists;
            } else if (selectedDept == "JHS") {
                let level_lists = [
                    { label: "All", value: "[7,8,9,10]" },
                    { label: "9-10th grade", value: "[9,10]" },
                    { label: "8-10th grade", value: "[8,9,10]" },
                    { label: "8-9th grade", value: "[8,9]" },
                    { label: "7-9th grade", value: "[7,8,9]" },
                    { label: "7-8th grade", value: "[7,8]" },
                    { label: "10th grade", value: "[10]" },
                    { label: "9th grade", value: "[9]" },
                    { label: "8th grade", value: "[8]" },
                    { label: "7th grade", value: "[7]" },
                ];

                this.level_lists = level_lists;
            } else if (selectedDept == "HS") {
                let level_lists = [
                    { label: "All", value: "[7,8,9,10,11,12]" },
                    { label: "K-11", value: "[11]" },
                    { label: "K-12", value: "[12]" },
                    { label: "7-K11 grade", value: "[7,8,9,10,11]" },
                    { label: "9-10th grade", value: "[9,10]" },
                    { label: "8-10th grade", value: "[8,9,10]" },
                    { label: "8-9th grade", value: "[8,9]" },
                    { label: "7-9th grade", value: "[7,8,9]" },
                    { label: "7-8th grade", value: "[7,8]" },
                    { label: "10th grade", value: "[10]" },
                    { label: "9th grade", value: "[9]" },
                    { label: "8th grade", value: "[8]" },
                    { label: "7th grade", value: "[7]" },
                ];

                this.level_lists = level_lists;
            } else if (selectedDept == "GS") {
                let level_lists = [{ label: "None", value: "none" }];

                this.level_lists = level_lists;
            } else if (selectedDept == "SHS") {
                let level_lists = [
                    { label: "All", value: "[11,12]" },
                    { label: "K-11", value: "[11]" },
                    { label: "K-12", value: "[12]" },
                ];

                this.level_lists = level_lists;
            } else if (selectedDept == "PRE-K") {
                let level_lists = [
                    { label: "All", value: "[1,2]" },
                    { label: "Kinder 1", value: "[1]" },
                    { label: "Kinder 2", value: "[12]" },
                ];

                this.level_lists = level_lists;
            }
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
                month: "long",
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
                console.error("No events available.");
                this.filteredEvents = [];
                return;
            }

            const date = new Date(currentYear, selectedMonth - 1, day);

            const formattedDate = date.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            });

            this.dateSelected = formattedDate;
            this.daySelected = day;

            const formattedInputDate = date.toISOString().split("T")[0];

            this.filteredEvents = eventsWithDetails.filter((event) => {
                return (
                    event.date_start === formattedInputDate ||
                    event.date_end === formattedInputDate
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
};
</script>
