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
                        <th class="px-4 py-2 text-center w-8">Actions</th>
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
                                    user_role == 'event_coordinator' ||
                                    user_role == 'super_admin'
                                "
                                @click="eventUpdate(event.event_id)"
                                class="text-yellow-500 hover:text-yellow-700"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                v-if="
                                    user_role == 'event_coordinator' ||
                                    user_role == 'super_admin'
                                "
                                @click="eventDelete(event.event_id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>

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
                                    <a
                                        :href="
                                            '/admin/event-delete/' +
                                            event.event_id
                                        "
                                        class="px-4 py-2 bg-red-500 text-red-100 rounded hover:opacity-50"
                                        >Yes</a
                                    >
                                </div>
                            </div>
                        </div>
                        <!-- /delete form -->

                        <!-- Edit Form -->
                        <div
                            :id="'event-update-' + event.event_id"
                            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
                        >
                            <form
                                @submit.prevent="submitForm"
                                enctype="multipart/form-data"
                                class="bg-white p-2 w-[500px] rounded shadow-md"
                            >
                                <div class="flex justify-between">
                                    <h1 class="text-2xl font-semibold">
                                        Update Details
                                    </h1>
                                    <button
                                        type="button"
                                        @click="eventUpdate(event.event_id)"
                                        class="text-2xl font-bold hover:opacity-50"
                                    >
                                        &times;
                                    </button>
                                </div>
                                <input
                                    type="hidden"
                                    name="id"
                                    :value="event.event_id"
                                />
                                <div>
                                    <label for="">Title:</label>
                                    <input
                                        :value="event.event_name"
                                        name="event_name"
                                        type="text"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>
                                <div>
                                    <label for="">Date Start:</label>
                                    <input
                                        type="date"
                                        :value="event.date_start"
                                        name="date_start"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>

                                <div>
                                    <label for="">Time Start:</label>
                                    <input
                                        type="time"
                                        :value="event.time_start"
                                        name="time_start"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>
                                <div>
                                    <label for="">Date End:</label>
                                    <input
                                        type="date"
                                        :value="event.date_end"
                                        name="date_end"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>

                                <div>
                                    <label for="">Time End:</label>
                                    <input
                                        type="time"
                                        :value="event.time_end"
                                        name="time_end"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                    />
                                </div>

                                <div>
                                    <label for="">Venue:</label>
                                    <select
                                        name="venue_id"
                                        class="block p-2 border border-gray-300 rounded w-full"
                                    >
                                        <option :value="event.venue_id">
                                            {{ event.venue_name }}
                                        </option>
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
                                    <label for="">Term:</label>
                                    <select
                                        name="term_id"
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
                                    <label for="">Departments:</label>
                                    <select
                                        name="department_id"
                                        class="block p-2 border border-gray-300 w-full rounded"
                                        required
                                    >
                                        <option :value="event.department_id">
                                            {{ event.department_name }}
                                        </option>
                                        <option
                                            v-for="department in departments"
                                            :key="department.id"
                                            :value="department.id"
                                        >
                                            {{ department.name }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="">Acitivity Design:</label>
                                    <input
                                        type="file"
                                        name="activity_design"
                                        class="block p-2 border border-gray-300 rounded w-full"
                                    />
                                </div>

                                <div class="flex justify-end space-x-1 py-2">
                                    <button
                                        @click="eventUpdate(event.event_id)"
                                        type="button"
                                        class="px-4 py-2 border border-gray-300 text-gray-800 hover:opacity-50 rounded"
                                    >
                                        Close
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                    >
                                        Update
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
                                    <h1 class="text-2xl font-bold">
                                        Event Details
                                    </h1>

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
                                    <span>{{ event.department_name }} </span>
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
                                            {{
                                                formatTime(event.time_start)
                                            }}</span
                                        >
                                        To
                                        <span
                                            >{{ event.date_end }}
                                            {{
                                                formatTime(event.time_end)
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <div class="flex justify-between p-2">
                                    <h1 class="font-medium">
                                        Activity Design:
                                    </h1>
                                    <a
                                        title="Click to download"
                                        :href="
                                            '/admin/download-activity-design/' +
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
                                        '/admin/event/comment-add/' +
                                        event.event_id
                                    "
                                    class="flex flex-col p-2"
                                >
                                    <textarea
                                        v-if="
                                            user_role == 'venue_coordinator' &&
                                            event.comment == null &&
                                            event.isApprovedByVenueCoordinator ==
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
                                                user_role ==
                                                    'venue_coordinator' &&
                                                event.comment == null &&
                                                event.isApprovedByVenueCoordinator ==
                                                    null
                                            "
                                            type="submit"
                                            class="px-4 py-2 bg-red-500 text-red-100 hover:opacity-50 rounded"
                                        >
                                            Decline
                                        </button>

                                        <button
                                            type="button"
                                            @click="
                                                venueApproveConfirm(
                                                    event.event_id
                                                )
                                            "
                                            v-if="
                                                user_role ==
                                                    'venue_coordinator' &&
                                                event.isApprovedByVenueCoordinator ==
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
                                        '/admin/event/comment-add/' +
                                        event.event_id
                                    "
                                    method="GET"
                                    v-if="
                                        uer_role == 'admin' &&
                                        event.isApprovedByAdmin == null
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
                                        user_role == 'event_coordinator' ||
                                        (user_role == 'super_admin' &&
                                            event.isApprovedByVenueCoordinator !=
                                                null &&
                                            event.isApprovedByAdmin != null)
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
                                        event.isApprovedByVenueCoordinator !=
                                            null &&
                                        event.isApprovedByAdmin == null
                                    "
                                    class="flex justify-end space-x-1 p-2"
                                >
                                    <button
                                        type="button"
                                        class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded"
                                    >
                                        Decline
                                    </button>
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
                                        event.isApprovedByVenueCoordinator !=
                                            null &&
                                        event.isApprovedByAdmin == null
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
                                                :id="
                                                    'retract-btn-' +
                                                    event.event_id
                                                "
                                                class="fas fa-chevron-down"
                                            ></i>
                                        </button>
                                    </div>

                                    <form
                                        v-if="
                                            user_role == 'venue_coordinator' &&
                                            event.isApprovedByAdmin == null
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
                                    :id="
                                        'venue-approve-confirm-' +
                                        event.event_id
                                    "
                                    class="flex fixed inset-0 justify-center bg-gray-800 bg-opacity-50 items-center hidden z-50"
                                >
                                    <div class="bg-white rounded">
                                        <h1 class="p-2 text-xl">
                                            Are you sure to confirm this venue?
                                        </h1>
                                        <div
                                            class="flex p-2 justify-end space-x-1"
                                        >
                                            <button
                                                @click="
                                                    venueApproveConfirm(
                                                        event.event_id
                                                    )
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
                                        event.isApprovedByAdmin != null
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
                                        :id="
                                            'retract-venue-confirm-' +
                                            event.event_id
                                        "
                                        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden"
                                    >
                                        <div
                                            class="bg-white p-2 rounded shadow-md"
                                        >
                                            <h1 class="text-xl">
                                                Are you sure to approve this
                                                venue?
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
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const eventDelete = (id) => {
    document.getElementById("event-delete-" + id).classList.toggle("hidden");
};
const eventUpdate = (id) => {
    document.getElementById("event-update-" + id).classList.toggle("hidden");
};

const eventView = (id) => {
    document.getElementById("event-preview-" + id).classList.toggle("hidden");
};
export default {
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
    },
    methods: {
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
      <div><h2>Deparment: ${event.department_name}</h2></div>
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
    },
};
</script>
