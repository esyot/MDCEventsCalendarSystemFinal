<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";
import { Inertia } from "@inertiajs/inertia";

defineOptions({ layout: MainLayout });

const venueCoordinatorRetractForm = (eventId) => {
    document
        .getElementById("venueCoordinatorRetractForm-" + eventId)
        .classList.toggle("hidden");

    document
        .getElementById("retract-btn-" + eventId)
        .classList.toggle("fa-chevron-down");
    document
        .getElementById("retract-btn-" + eventId)
        .classList.toggle("fa-chevron-up");
};

const retractConfirm = (eventId) => {
    document
        .getElementById("retract-venue-confirm-" + eventId)
        .classList.toggle("hidden");
};

const venueApproveConfirm = (id) => {
    document
        .getElementById("venue-approve-confirm-" + id)
        .classList.toggle("hidden");
};
</script>

<template>
    <div class="overflow-x-auto mx-4 shadow-lg shadow-gray-300">
        <div class="h-[600px] overflow-y-auto mt-4">
            <table class="min-w-full bg-white border border-gray-300">
                <thead
                    class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                >
                    <tr class="bg-gray-300">
                        <th class="px-4 py-2 text-left text-center">
                            Event Name
                        </th>
                        <th class="px-4 py-2 text-left text-center">
                            Requested by
                        </th>

                        <th
                            v-if="user_role == 'venue_coordinator'"
                            class="px-4 py-2 text-center"
                        >
                            Requested Venue
                        </th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(event, index) in events"
                        :key="index"
                        class="bg-white border-b h-[40px]"
                    >
                        <td class="px-4 py-2 text-center">
                            {{ event.event_name }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ event.user_fname }}
                            {{ event.user_lname }}
                        </td>

                        <td
                            v-if="user_role == 'venue_coordinator'"
                            class="text-center"
                        >
                            {{ event.venue_name }} at {{ event.venue_building }}
                        </td>
                        <td class="px-4 py-2 space-x-2 text-center">
                            <small>Venue Coordinator:</small>
                            <i
                                :class="
                                    event.approved_by_venue_coordinator_at !=
                                    null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                            <small>Admin:</small>
                            <i
                                :class="
                                    event.approved_by_admin_at != null
                                        ? 'fas fa-check text-green-500'
                                        : 'fas fa-x text-red-500'
                                "
                            ></i>
                        </td>
                        <td class="px-4 py-2 space-x-2 text-center flex">
                            <button
                                id="eventCoordinator"
                                v-if="
                                    user_role == 'venue_coordinator' ||
                                    user_role == 'super_admin' ||
                                    user_role == 'admin' ||
                                    user_role == 'event_coordinator'
                                "
                                @click="eventView(event.event_id)"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <i class="fas fa-eye"></i>
                            </button>

                            <button
                                v-if="
                                    event.user_id == user.id &&
                                    user_role != 'venue_coordinator' &&
                                    event.approved_by_admin_at == null
                                "
                                @click="eventUpdate(event)"
                                class="text-yellow-500 hover:text-yellow-700"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                v-if="
                                    (user_role == 'event_coordinator' &&
                                        event.approved_by_admin_at == null) ||
                                    (user_role == 'super_admin' &&
                                        event.approved_by_admin_at == null) ||
                                    (user_role == 'admin' &&
                                        event.approved_by_admin_at == null)
                                "
                                @click="eventDelete(event.event_id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- loop -->

            <div v-for="(event, index) in events" :key="index">
                <!-- delete form -->

                <div
                    :id="'event-delete-' + event.event_id"
                    class="flex fixed inset-0 bg-gray-800 bg-opacity-50 justify-center hidden items-center z-50"
                >
                    <div class="bg-white p-2 shadow-md rounded">
                        <div>
                            <h1>Are you sure to delete this event?</h1>
                        </div>
                        <div class="flex justify-end p-2 space-x-1">
                            <button
                                @click="eventDelete(event.event_id)"
                                class="px-4 py-2 border border-gray-300 rounded hover:opacity-50"
                            >
                                No
                            </button>
                            <button
                                @click="eventDeleteConfirm(event.event_id)"
                                class="px-4 py-2 bg-red-500 text-red-100 rounded hover:opacity-50"
                            >
                                Yes
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /delete form -->

                <!-- Edit Form -->
                <div
                    :id="'update-event-' + event.event_id"
                    class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden"
                >
                    <form
                        @submit.prevent="submitForm"
                        enctype="multipart/form-data"
                        class="bg-white p-2 w-[500px] shadow-md rounded"
                    >
                        <input
                            type="hidden"
                            :value="event.event_id"
                            name="id"
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

                        <div class="flex w-full justify-between mb-4">
                            <h1 class="text-2xl">Update Details</h1>

                            <button
                                type="button"
                                @click="eventUpdate(event)"
                                class="text-2xl font-bold hover:opacity-50"
                            >
                                &times;
                            </button>
                        </div>

                        <div class="flex flex-col">
                            <div class="w-full">
                                <label for="">Venue:</label>

                                <select
                                    name="event_venue"
                                    :value="event.venue_id"
                                    class="block p-2.5 border border-gray-300 w-full rounded"
                                    required
                                    @change="
                                        onVenueChange(
                                            selectedDateForm,
                                            $event.target.value,
                                            events
                                        )
                                    "
                                >
                                    <option value=""></option>
                                    <option
                                        v-for="venue in venues"
                                        :key="venue"
                                        :value="venue.id"
                                    >
                                        {{ venue.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex justify-between space-x-1">
                                <div class="w-full">
                                    <label for="">Date Start:</label>
                                    <input
                                        type="date"
                                        name="event_date_start"
                                        id=""
                                        v-model="selectedDateStartForm"
                                        @change="
                                            onVenueChange(
                                                $event.target.value,
                                                selectedVenue,
                                                eventsWithDetails
                                            )
                                        "
                                        class="block p-2 border border-gray-300 w-full rounded"
                                        required
                                    />
                                </div>

                                <div class="w-full">
                                    <label for="">Date End:</label>
                                    <input
                                        type="date"
                                        name="event_date_end"
                                        v-model="selectedDateEndForm"
                                        @change="
                                            onVenueChange(
                                                $event.target.value,
                                                selectedVenue,
                                                eventsWithDetails
                                            )
                                        "
                                        class="block p-2 border border-gray-300 w-full rounded"
                                        required
                                    />
                                </div>
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
                                        >Start Time:</label
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
                                            v-model="selectedAMPMStart"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeStartPeriodChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option>AM</option>
                                            <option>PM</option>
                                        </select>
                                        <select
                                            id="hourStart"
                                            name="hourStart"
                                            v-model="selectedHourStart"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeStartHourChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option
                                                v-for="hour in hours"
                                                :key="hour"
                                                :disabled="
                                                    isHourDisabled(
                                                        hour,
                                                        selectedAMPMStart
                                                    )
                                                "
                                            >
                                                {{ hour }}
                                            </option>
                                        </select>

                                        <select
                                            id="minuteStart"
                                            name="minuteStart"
                                            v-model="selectedMinuteStart"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeStartMinutesChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option
                                                v-for="minute in minutes"
                                                :key="minute"
                                            >
                                                {{ minute }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <label
                                        for="ampmEnd"
                                        class="block text-sm font-medium text-gray-700"
                                        >End Time:</label
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
                                            v-model="selectedAMPMEnd"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeEndPeriodChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option></option>
                                            <option>AM</option>
                                            <option>PM</option>
                                        </select>
                                        <select
                                            id="hourEnd"
                                            name="hourEnd"
                                            v-model="selectedHourEnd"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeEndHourChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option
                                                v-for="hour in hours"
                                                :key="hour"
                                                :disabled="
                                                    isHourDisabled(
                                                        hour,
                                                        selectedAMPMEnd
                                                    )
                                                "
                                                required
                                            >
                                                {{ hour }}
                                            </option>
                                        </select>
                                        <select
                                            id="minuteEnd"
                                            name="minuteEnd"
                                            v-model="selectedMinuteEnd"
                                            class="border p-2 rounded focus:outline-none"
                                            @change="
                                                timeEndMinutesChange(
                                                    $event.target.value
                                                )
                                            "
                                            required
                                        >
                                            <option
                                                v-for="minute in minutes"
                                                :key="minute"
                                            >
                                                {{ minute }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="">Event Title:</label>
                            <input
                                type="text"
                                name="event_name"
                                v-model="selectedEventName"
                                placeholder="Input event title"
                                class="w-full block p-2 border border-gray-300 rounded shadow-inner"
                                required
                            />
                        </div>
                        <div class="flex space-x-2">
                            <div class="w-full">
                                <label for="">Departments:</label>
                                <div class="flex p-2 border rounded flex-col">
                                    <span
                                        v-for="department in departmentsForm"
                                        :key="department.department_id"
                                        class="space-x-1"
                                    >
                                        <input
                                            @change="
                                                updateSelectedDepartments(
                                                    $event.target.checked,
                                                    department.department_id
                                                )
                                            "
                                            class="space-x-1"
                                            type="checkbox"
                                            :value="department.department_id"
                                            v-model="departmentsChecked"
                                            :checked="
                                                isSelectedDepartment(
                                                    department.department_id.toString()
                                                )
                                            "
                                        />

                                        <span>{{ department.acronym }}</span>
                                    </span>
                                </div>
                            </div>

                            <input
                                type="hidden"
                                :value="selectedDepartments"
                                name="departments[]"
                            />
                            <div>
                                <div>
                                    <label for="">Term:</label>
                                    <select
                                        name="event_term_id"
                                        id=""
                                        class="w-full block p-2 border border-gray-300 rounded shadow-inner"
                                        required
                                    >
                                        <option :value="event.term_id">
                                            {{ event.term_name }}
                                        </option>
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
                                    <label for="event_levels">Levels</label>
                                    <select
                                        name="event_levels[]"
                                        id="level"
                                        multiple
                                        class="w-full"
                                        required
                                    >
                                        <option value="k1">
                                            Kindergarten 1
                                        </option>
                                        <option value="k2">
                                            Kindergarten 2
                                        </option>

                                        <option value="g1">Grade 1</option>
                                        <option value="g2">Grade 2</option>
                                        <option value="g3">Grade 3</option>
                                        <option value="g4">Grade 4</option>
                                        <option value="g5">Grade 5</option>
                                        <option value="g6">Grade 6</option>

                                        <option value="g7">Grade 7</option>
                                        <option value="g8">Grade 8</option>
                                        <option value="g9">Grade 9</option>
                                        <option value="g10">Grade 10</option>

                                        <option value="g11">Grade 11</option>
                                        <option value="g12">Grade 12</option>

                                        <option value="c1">
                                            1st Year College
                                        </option>
                                        <option value="c2">
                                            2nd Year College
                                        </option>
                                        <option value="c3">
                                            3rd Year College
                                        </option>
                                        <option value="c4">
                                            4th Year College
                                        </option>
                                        <option value="c5">
                                            5th Year College
                                        </option>
                                        <option value="cQ">Qualifying</option>

                                        <option value="m1">Masters 1</option>
                                        <option value="m2">Masters 2</option>
                                        <option value="d1">Doctors 1</option>
                                        <option value="d2">Doctors 2</option>
                                    </select>
                                </div>

                                <div>
                                    <label for=""
                                        >Activity Design (PDF file only):</label
                                    >
                                    <input
                                        type="file"
                                        name="activity_design"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between"></div>

                        <div class="flex justify-end p-2">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                            >
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /Edit Form -->

                <!-- Event Details -->
                <div
                    :id="'event-preview-' + event.event_id"
                    class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
                >
                    <div
                        class="select-none bg-white w-[600px] rounded shadow-md"
                    >
                        <div class="flex justify-between p-2">
                            <h1 class="text-2xl font-bold">Event Details</h1>

                            <button
                                @click="eventView(event.event_id)"
                                class="text-xl font-bold hover:opacity-50"
                            >
                                &times;
                            </button>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Event Name:</h1>
                            <span>{{ event.event_name }}</span>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Event Venue:</h1>
                            <span
                                >{{ event.venue_name }} at
                                {{ event.venue_building }}</span
                            >
                        </div>
                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Department:</h1>
                            <span>{{ event.department_acronyms }} </span>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Term:</h1>
                            <span>{{ event.term_name }} </span>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Level:</h1>
                            <span>{{ event.levels }} </span>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Date & Time:</h1>
                            <div>
                                <span
                                    >{{ event.date_start }}
                                    {{ formatTime(event.time_start) }}</span
                                >
                                To
                                <span
                                    >{{ event.date_end }}
                                    {{ formatTime(event.time_end) }}</span
                                >
                            </div>
                        </div>

                        <div class="flex justify-between p-2">
                            <h1 class="font-medium">Activity Design:</h1>
                            <a
                                title="Click to view"
                                :href="
                                    '/admin/view-activity-design/' +
                                    event.activity_design_file_name
                                "
                                class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                            >
                                {{ event.activity_design_file_name }}
                            </a>
                        </div>
                        <div class="p-2">
                            <h1 class="font-medium">Comment:</h1>
                            <span>{{ event.comment }}</span>
                        </div>

                        <form
                            :action="
                                '/admin/event/comment-add/' + event.event_id
                            "
                            class="flex flex-col p-2"
                        >
                            <textarea
                                v-if="
                                    user_role == 'venue_coordinator' &&
                                    event.comment == null &&
                                    event.approved_by_venue_coordinator_at ==
                                        null
                                "
                                class="border rounded p-2 m-2"
                                placeholder="Input decline message"
                                name="comment"
                                id=""
                                height="10"
                                required
                            ></textarea>
                            <div class="flex justify-end space-x-1">
                                <button
                                    v-if="
                                        user_role == 'venue_coordinator' &&
                                        event.comment == null &&
                                        event.approved_by_venue_coordinator_at ==
                                            null
                                    "
                                    type="submit"
                                    class="px-4 py-2 bg-red-500 text-red-100 hover:opacity-50 rounded"
                                >
                                    Decline
                                </button>

                                <button
                                    type="button"
                                    @click="venueApproveConfirm(event.event_id)"
                                    v-if="
                                        user_role == 'venue_coordinator' &&
                                        event.approved_by_venue_coordinator_at ==
                                            null
                                    "
                                    class="px-4 py-2 bg-green-500 text-green-100 hover:opacity-50 rounded"
                                >
                                    Approve Venue
                                </button>
                            </div>
                        </form>

                        <form
                            :action="
                                '/admin/event/comment-add/' + event.event_id
                            "
                            method="GET"
                            v-if="
                                user_role == 'admin' &&
                                event.approved_by_admin_at == null
                            "
                            class="p-2"
                        >
                            <h1 class="font-medium">Comment:</h1>
                            <div class="flex items-start space-x-2">
                                <textarea
                                    height="2"
                                    name="comment"
                                    :value="event.comment"
                                    placeholder="Input comment here..."
                                    class="border shadow-inner w-full border-gray-300 rounded"
                                ></textarea>
                                <button
                                    class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                >
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>

                        <div
                            v-if="
                                (user_role == 'super_admin' &&
                                    event.approved_by_venue_coordinator_at !=
                                        null &&
                                    event.approved_by_admin_at != null) ||
                                (user_role == 'event_coordinator' &&
                                    event.approved_by_venue_coordinator_at !=
                                        null &&
                                    event.approved_by_admin_at != null)
                            "
                            class="flex p-2 justify-end"
                        >
                            <button
                                @click="printDiv(event)"
                                class="px-4 py-2 bg-green-500 text-green-100 hover:opacity-50 rounded"
                            >
                                Print Stub
                            </button>
                        </div>

                        <div
                            id="approve-admin"
                            v-if="
                                user_role == 'admin' &&
                                event.approved_by_venue_coordinator_at !=
                                    null &&
                                event.approved_by_admin_at == null
                            "
                            class="flex justify-end space-x-1 p-2"
                        >
                            <!-- <a
                                :href="
                                    '/admin/event/decline/admin/' +
                                    event.event_id
                                "
                                class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                            >
                                Decline
                            </a> -->
                            <a
                                :href="
                                    '/admin/event/approve/admin/' +
                                    event.event_id
                                "
                                type="button"
                                class="px-4 py-2 bg-green-500 text-green-100 rounded"
                                >Approve</a
                            >
                        </div>
                        <div
                            id="retract-venue-coordinator"
                            v-if="
                                user_role == 'venue_coordinator' &&
                                event.approved_by_venue_coordinator_at !=
                                    null &&
                                event.approved_by_admin_at == null
                            "
                            class="space-y-2 p-2"
                        >
                            <div class="flex justify-end">
                                <button
                                    @click="
                                        venueCoordinatorRetractForm(
                                            event.event_id
                                        )
                                    "
                                    type="button"
                                    class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                                >
                                    Retract
                                    <i
                                        :id="'retract-btn-' + event.event_id"
                                        class="fas fa-chevron-down"
                                    ></i>
                                </button>
                            </div>

                            <form
                                v-if="
                                    user_role == 'venue_coordinator' &&
                                    event.approved_by_admin_at == null
                                "
                                :action="
                                    '/event-request/retract/' +
                                    user_role +
                                    '/' +
                                    event.event_id
                                "
                                method="GET"
                                :id="
                                    'venueCoordinatorRetractForm-' +
                                    event.event_id
                                "
                                class="hidden"
                            >
                                <div>
                                    <label for="">Comment:</label>
                                    <textarea
                                        name="comment"
                                        placeholder="Input comment here..."
                                        class="w-full h-10 border rounded"
                                        required
                                    ></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- venue approve form -->
                        <div
                            :id="'venue-approve-confirm-' + event.event_id"
                            class="flex fixed inset-0 justify-center bg-gray-800 bg-opacity-50 items-center hidden z-50"
                        >
                            <div class="bg-white rounded">
                                <h1 class="p-2 text-xl">
                                    Are you sure to confirm this venue?
                                </h1>
                                <div class="flex p-2 justify-end space-x-1">
                                    <button
                                        @click="
                                            venueApproveConfirm(event.event_id)
                                        "
                                        class="px-4 py-2 border border-gray-300 rounded text-gray-500"
                                    >
                                        No
                                    </button>
                                    <a
                                        :href="
                                            '/admin/event/approve/venue_coordinator/' +
                                            event.event_id
                                        "
                                        class="px-4 py-2 text-green-100 bg-green-500 rounded"
                                        >Yes</a
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- /venue approve form -->
                        <div
                            v-if="
                                user_role == 'admin' &&
                                event.approved_by_admin_at != null
                            "
                            id="approve-admin"
                            class="flex justify-end space-x-1 p-2"
                        >
                            <a
                                :href="
                                    '/event-request/retract/' +
                                    user_role +
                                    '/' +
                                    event.event_id
                                "
                                type="button"
                                class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                                >Retract
                            </a>
                        </div>

                        <div
                            :id="event.event_id"
                            class="flex justify-end space-x-1 p-2 z-50"
                        >
                            <div
                                :id="'retract-venue-confirm-' + event.event_id"
                                class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden"
                            >
                                <div class="bg-white p-2 rounded shadow-md">
                                    <h1 class="text-xl">
                                        Are you sure to approve this venue?
                                    </h1>
                                    <div
                                        class="flex justify-end space-x-1 mt-2"
                                    >
                                        <button
                                            @click="
                                                venueApproveConfirm(
                                                    event.event_id
                                                )
                                            "
                                            class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                                        >
                                            No
                                        </button>

                                        <a
                                            :href="
                                                '/admin/event/approve/venue_coordinator/' +
                                                event.event_id
                                            "
                                            class="px-4 py-2 bg-green-500 text-green-100 rounded"
                                            >Yes</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /loop -->
        </div>
    </div>
</template>

<script>
import axios from "axios";

import { router } from "@inertiajs/vue3";

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

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const eventDelete = (id) => {
    document.getElementById("event-delete-" + id).classList.toggle("hidden");
};

const eventView = (id) => {
    document.getElementById("event-preview-" + id).classList.toggle("hidden");
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

            startTime: null,
            endTime: null,

            selectedAMPMStart: null,
            selectedAMPMEnd: null,
            selectedHourStart: null,
            selectedMinuteStart: null,
            selectedHourEnd: null,
            selectedMinuteEnd: null,

            selectedDateForm: "",
            selectedDateStartForm: "",
            selectedDateEndForm: "",

            startTimeApproved: false,
            endTimeApproved: false,
            startTimeDisable: true,

            selectedEventName: null,

            departmentSelected: "",

            departmentsChecked: [],
        };
    },
    props: {
        events: {
            type: Object,
        },
        user_role: {},
        venues: {
            type: Object,
        },
        departments: {},
        terms: {
            type: Object,
        },
        departmentsForm: {},
    },
    methods: {
        updateSelectedDepartments(event, departmentId) {
            if (!Array.isArray(this.selectedDepartments)) {
                this.selectedDepartments = [];
            }

            if (event) {
                if (
                    !this.selectedDepartments.includes(departmentId.toString())
                ) {
                    this.selectedDepartments.push(departmentId.toString());
                }
            } else {
                this.selectedDepartments = this.selectedDepartments.filter(
                    (id) => id !== departmentId.toString()
                );
            }

            console.log(
                "Updated department selection:",
                this.selectedDepartments
            );
        },
        isSelectedDepartment(departmentId) {
            let arrayedString;

            if (typeof this.selectedDepartments === "string") {
                arrayedString = this.selectedDepartments
                    .split(",")
                    .map((item) => item.trim());
            } else if (Array.isArray(this.selectedDepartments)) {
                arrayedString = this.selectedDepartments.map((item) =>
                    item.toString().trim()
                );
            } else {
                return false;
            }

            return arrayedString.includes(departmentId.toString().trim());
        },
        eventUpdate(event) {
            const date_start = event.date_start;
            const date_end = event.date_end;
            const time_start = event.time_start;
            const time_end = event.time_end;

            this.selectedDepartments = event.department_id;
            this.departmentSelected = event.department_id;

            this.startTime = event.time_start;
            this.endTime = event.time_end;

            const timeObj_start = new Date(`1970-01-01T${time_start}`);
            let hour_start = timeObj_start.getHours();
            let period_start = hour_start < 12 ? "AM" : "PM";
            hour_start = hour_start % 12 || 12;
            hour_start = hour_start.toString().padStart(2, "0");

            const minute_start = timeObj_start
                .getMinutes()
                .toString()
                .padStart(2, "0");

            const timeObj_end = new Date(`1970-01-01T${time_end}`);
            let hour_end = timeObj_end.getHours();
            let period_end = hour_end < 12 ? "AM" : "PM";
            hour_end = hour_end % 12 || 12;
            hour_end = hour_end.toString().padStart(2, "0");

            const minute_end = timeObj_end
                .getMinutes()
                .toString()
                .padStart(2, "0");

            const deptIds = event.department_id
                .split(",")
                .map((id) => parseInt(id));

            const departmentList = this.departmentsForm;

            const filteredDepartments = departmentList.filter((department) => {
                return deptIds.includes(department.id);
            });

            this.selectedDateStartForm = date_start;
            this.selectedDateEndForm = date_end;
            this.selectedAMPMStart = period_start;
            this.selectedHourStart = hour_start;
            this.selectedMinuteStart = minute_start;
            this.selectedAMPMEnd = period_end;
            this.selectedHourEnd = hour_end;
            this.selectedMinuteEnd = minute_end;
            this.selectedEventName = event.event_name;

            document
                .getElementById("update-event-" + event.event_id)
                .classList.toggle("hidden");

            this.departmentSelected = event.department_id;
        },
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

            const formatDate = (dateObj) => {
                const year = dateObj.getFullYear();
                const month = dateObj.getMonth() + 1;
                const day = dateObj.getDate();
                return `${year}-${month.toString().padStart(2, "0")}-${day
                    .toString()
                    .padStart(2, "0")}`;
            };

            if (typeof date === "object" && date instanceof Date) {
                date_end = formatDate(date);
            }

            if (typeof date_start === "object" && date_start instanceof Date) {
                date_start = formatDate(date_start);
            }

            const filteredEvents = events.filter((event) => {
                if (
                    event.venue_id === parseInt(venueId) &&
                    event.approved_by_admin_at !== null
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
                hour24 += 12;
            } else if (period === "AM" && hour24 === 12) {
                hour24 = 0;
            }
            return hour24;
        },
        isHourDisabled(hour, selectedTimePeriod) {
            const disabledHours = this.unavailableTimes.flatMap((range) => {
                const startHour = this.convertTimeToHour(range.start);
                const endHour = this.convertTimeToHour(range.end);
                let hoursInRange = [];

                if (startHour < 12 && endHour < 12) {
                    for (let h = startHour; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                } else if (startHour < 12 && endHour >= 12) {
                    for (let h = startHour; h < 12; h++) {
                        hoursInRange.push(h % 24);
                    }

                    for (let h = 12; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                } else if (startHour >= 12 && endHour >= 12) {
                    for (let h = startHour; h <= endHour; h++) {
                        hoursInRange.push(h % 24);
                    }
                }

                return hoursInRange;
            });

            const hourInt = parseInt(hour);
            let hour24 = hourInt;

            if (selectedTimePeriod === "PM" && hourInt !== 12) {
                hour24 = hourInt + 12;
            }

            return disabledHours.includes(hour24);
        },
        updateStartTime() {
            let hour = this.startHour;
            let minutes = this.startMinutes;
            let period = this.startPeriod ? this.startPeriod.toUpperCase() : "";

            if (period === "PM" && hour < 12) hour = parseInt(hour) + 12;
            if (period === "AM" && hour == 12) hour = 0;

            const formattedStartTime = new Date();
            formattedStartTime.setHours(hour);
            formattedStartTime.setMinutes(minutes);

            this.startTime = formattedStartTime;

            this.watchTimeChange();
        },

        updateEndTime() {
            let hour = this.endHour;
            let minutes = this.endMinutes;
            let period = this.endPeriod ? this.endPeriod.toUpperCase() : "";

            if (period === "PM" && hour < 12) hour = parseInt(hour) + 12;
            if (period === "AM" && hour == 12) hour = 0;

            const formattedEndTime = new Date();
            formattedEndTime.setHours(hour);
            formattedEndTime.setMinutes(minutes);

            this.endTime = formattedEndTime;

            this.watchTimeChange();
        },

        isTimeOverlapping(startTime, endTime, rangeStart, rangeEnd) {
            return startTime < rangeEnd && endTime > rangeStart;
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

            return hours * 60 + minutes;
        },
        rangesOverlap(range1, range2) {
            const range1Start = this.convertTimeToMinutes(range1.start);
            const range1End = this.convertTimeToMinutes(range1.end);
            const range2Start = this.convertTimeToMinutes(range2.start);
            const range2End = this.convertTimeToMinutes(range2.end);

            return range1Start < range2End && range1End > range2Start;
        },

        watchTimeChange() {
            let date = this.startTime;

            let hour = date.getHours();
            let minutes = date.getMinutes();

            let period = hour >= 12 ? "PM" : "AM";

            let formattedHour = hour % 12;
            if (formattedHour === 0) formattedHour = 12;

            if (formattedHour != null && minutes != null && period != null) {
                let timeString = `${formattedHour}:${
                    minutes < 10 ? "0" + minutes : minutes
                } ${period}`;

                console.log(`Time String: ${timeString}`);

                this.startTimeApproved = true;
                this.startTimeDisable = false;
            } else {
                console.error("Invalid time components");
            }

            if (this.startTime && this.endTime) {
                const startTime = this.startTime.getTime();
                const endTime = this.endTime.getTime();

                if (endTime <= startTime) {
                    alert("End time must be greater than start time.");
                    this.selectedHourEnd = null;
                    this.selectedMinuteEnd = null;
                    this.endTime = null;
                    this.endTimeApproved = false;
                    return;
                }

                const formattedStartTime = this.formatSelectedTime(
                    this.startTime
                );
                const formattedEndTime = this.formatSelectedTime(this.endTime);

                const formattedTimes = {
                    start: formattedStartTime,
                    end: formattedEndTime,
                };

                let isOverlapping = false;

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
        formatTime(time) {
            const [hours, minutes] = time.split(":");
            const formattedHours = hours % 12 || 12;
            const ampm = hours < 12 ? "am" : "pm";
            return `${formattedHours}:${minutes} ${ampm}`;
        },
        printDiv(event) {
            const printWindow = window.open("", "", "height=500, width=800");

            printWindow.document.write(`
    <html><head><title>Event Details</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; text-align: center; }
        h2 { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        p { font-size: 14px; color: #555; margin-bottom: 10px; }
        div { margin-bottom: 15px; }
        .footer { display: flex; justify-content: end; flex-direction: column; align-items:center;}
        .footer h2 { text-decoration: underline; }
      </style>
    </head><body>
      <h1>Event Details</h1>
      <div><h2>Event: ${event.event_name}</h2></div>
      <div><h2>Deparment: ${event.department_acronyms}</h2></div>
      <div><h2>Venue: ${event.venue_name} at ${event.venue_building}</h2></div>
      <div><h2>Date: ${event.date_start} to  ${event.date_end} </h2></div>
      ${
          event.time_start && event.time_end
              ? `<div><h2>Time: ${this.formatTime(
                    event.time_start
                )} to ${this.formatTime(event.time_end)}</h2></div>`
              : ""
      }
      <div class="footer">
        <h2>JOSEPHINE D. APLACADOR, Ma.Ed.</h2>
        <i>OSAD Coordinator</i>
      </div>
    </body></html>
  `);

            printWindow.document.close();
            printWindow.print();
        },
        submitForm(event) {
            const formData = new FormData(event.target);

            Inertia.post("/admin/event-update", formData, {
                preserveState: true,
            });
        },
        eventDeleteConfirm(eventId) {
            router.delete(`/admin/event-delete/${eventId}`);
        },
    },
};
</script>
