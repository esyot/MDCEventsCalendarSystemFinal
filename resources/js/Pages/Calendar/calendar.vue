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

const isHasRecord = (day, month, year, events) => {
    const validEvents = Array.isArray(events) ? events : [];

    const date = new Date(year, month - 1, day);

    const dateString = date.toISOString().split("T")[0];

    return validEvents.some((event) => {
        const eventDateString = new Date(event).toISOString().split("T")[0];
        return eventDateString === dateString;
    });
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

    <!-- Modals Events Details -->
    <div
        id="eventsDetails"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-3/4 md:w-1/2 lg:w-1/3">
            <div class="flex justify-between p-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold">
                    Events on <span id="date-text"></span>
                </h1>
                <button
                    @click="closeEventsDetails()"
                    class="px-2 text-2xl font-bold hover:opacity-50"
                >
                    &times;
                </button>
            </div>
            <div class="p-4">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr>
                            <th
                                class="px-4 py-2 text-left font-medium text-gray-700 border-b"
                            >
                                Event Name
                            </th>
                            <th
                                class="px-4 py-2 text-left font-medium text-gray-700 border-b"
                            >
                                Date
                            </th>
                            <th
                                class="px-4 py-2 text-left font-medium text-gray-700 border-b"
                            >
                                Date End
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="event in filteredEvents" :key="event.id">
                            <td>{{ event.name }}</td>
                            <td>{{ event.date_start }}</td>
                            <td>{{ event.date_end }}</td>
                            <td>{{ event.date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--  -->

    <div class="flex flex-col">
        <div class="flex flex-col p-2 space-x-2">
            <div class="items-center flex space-x-2 p-2">
                <input
                    v-model="searchQuery"
                    placeholder="Search"
                    class="p-2 border rounded shadow-sm focus:outline-none"
                />
                <div>
                    <select
                        class="block p-2 border border-gray-300 w-full rounded"
                    >
                        <option value="">Department</option>
                        <option
                            v-for="department in departments"
                            :key="department"
                            :value="department.id"
                        >
                            {{ department.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="">
                <div
                    class="flex flex-col bg-gray-100 border-2 border-gray-300 rounded-xl"
                >
                    <div
                        class="w-full px-4 py-2 flex justify-between items-center border-b"
                    >
                        <button @click="changeYear('previous')" class="">
                            <i
                                class="fas fa-chevron-circle-left text-blue-500 fa-2xl hover:opacity-50"
                            ></i>
                        </button>
                        <select
                            v-model="currentYear"
                            class="px-4 py-2 shadow-inner border focus:outline-none"
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
                                class="fas fa-chevron-circle-right text-blue-500 fa-2xl hover:opacity-50"
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
                                        class="bg-blue-300 p-1 font-bold text-center"
                                    >
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
                                                        ? 'bg-blue-500'
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
                                    <span class="text-blue-100 text-2xl"
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
                                        class="flex justify-between"
                                    >
                                        <div :class="'flex p-2'">
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
                                            class="text-lg justify-center flex flex-col items-center"
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
                                                    ? 'hover:opacity-50 bg-blue-500'
                                                    : '',
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
                                            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden"
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

                                                <div>
                                                    <label for="">Term:</label>
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
                                                        >Departments:</label
                                                    >
                                                    <select
                                                        name="event_department_id"
                                                        class="block p-2 border border-gray-300 w-full rounded"
                                                        required
                                                    >
                                                        <option
                                                            v-for="department in departments"
                                                            :key="department"
                                                            :value="
                                                                department.id
                                                            "
                                                        >
                                                            {{
                                                                department.name
                                                            }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="">Levels</label>
                                                    <select
                                                        name="event_levels"
                                                        id=""
                                                        class="block p-2 border border-gray-300 rounded shadow-inner"
                                                        required
                                                    >
                                                        <option value="['1']">
                                                            1
                                                        </option>
                                                        <option
                                                            value="['2']"
                                                            id=""
                                                        >
                                                            2
                                                        </option>
                                                        <option
                                                            value="['3']"
                                                            id=""
                                                        >
                                                            3
                                                        </option>
                                                        <option
                                                            value="['4']"
                                                            id=""
                                                        >
                                                            4
                                                        </option>
                                                        <option
                                                            value="['1', '2']"
                                                            id=""
                                                        >
                                                            1-2
                                                        </option>
                                                        <option
                                                            value="['1','2', '3']"
                                                            id=""
                                                        >
                                                            1-3
                                                        </option>
                                                        <option
                                                            value="['1', '2', '3', '4']"
                                                            id=""
                                                        >
                                                            1-4
                                                        </option>
                                                        <option
                                                            value="['2', '3']"
                                                            id=""
                                                        >
                                                            2-3
                                                        </option>
                                                        <option
                                                            value="['2', '3', '4']"
                                                            id=""
                                                        >
                                                            2-4
                                                        </option>
                                                        <option
                                                            value="['3', '4']"
                                                            id=""
                                                        >
                                                            3-4
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
            formattedDate: "",
            filteredEvents: [],
        };
    },
    props: {
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
        eventsWithDetails: {
            type: Array,
        },
    },
    methods: {
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
            // Ensure eventsWithDetails is not null or undefined
            if (!eventsWithDetails || eventsWithDetails.length === 0) {
                console.error("No events available.");
                this.filteredEvents = []; // Reset filteredEvents
                return;
            }

            const date = new Date(currentYear, selectedMonth - 1, day);

            // Format the date in a readable format
            const formattedDate = date.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            });

            // Display the formatted date in your HTML
            document.getElementById("date-text").innerHTML = formattedDate;

            // Format the selected day into a 'yyyy-mm-dd' format for comparison
            const formattedInputDate = date.toISOString().split("T")[0]; // '2024-02-06'

            // Filter events that match the selected date
            this.filteredEvents = eventsWithDetails.filter((event) => {
                // Return events whose start or end date matches the selected day
                return (
                    event.date_start === formattedInputDate ||
                    event.date_end === formattedInputDate
                );
            });

            console.log(this.filteredEvents); // Log the filtered events for debugging

            // Toggle visibility of the event details container
            document.getElementById("eventsDetails").classList.toggle("hidden");
        },
        closeEventsDetails() {
            document.getElementById("eventsDetails").classList.toggle("hidden");
        },
    },
};

const openCreateEventModal = (day) => {
    document
        .getElementById("create-event-modal-" + day)
        .classList.remove("hidden");
};

const closeCreateEventModal = (day) => {
    document
        .getElementById("create-event-modal-" + day)
        .classList.add("hidden");
};
</script>
