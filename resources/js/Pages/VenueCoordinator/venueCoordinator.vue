<script setup>
import { ref } from "vue";
import MainLayout from "../../Layouts/MainLayout.vue";
import { Inertia } from "@inertiajs/inertia";

defineOptions({ layout: MainLayout });

defineProps({
    venueCoordinators: {
        type: Array,
        required: true,
    },
    venues: {
        type: Array,
    },
    user_venue: {
        type: Object,
    },
    venue_list: {
        type: Array,
    },
});

const closeVenuesModal = () => {
    document.getElementById("venuesModal").classList.toggle("hidden");
};

const removeVenueConfirm = (id) => {
    document
        .getElementById("remove-venue-confirm-" + id)
        .classList.toggle("hidden");
};
</script>

<template>
    <div
        id="venuesModal"
        v-if="venues != null"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white w-[500px] rounded shadow-md">
            <div class="flex items-center justify-between">
                <h1 class="text-lg px-2 font-semibold">
                    Venues Assigned with {{ user_venue.lname }},
                    {{ user_venue.fname }}
                </h1>
                <button
                    @click="closeVenuesModal()"
                    class="text-2xl px-2 font-bold hover:opacity-50"
                >
                    &times;
                </button>
            </div>
            <div class="px-4 mt-2">
                <h1>Venues:</h1>
                <span
                    v-if="venues.length == 0"
                    class="flex justify-center text-red-500"
                    >No venues assigned.</span
                >
                <ul v-for="venue in venues" :key="venue">
                    <li class="flex items-center justify-between border p-2">
                        <span>
                            {{ venue.name }} at
                            {{ venue.building }}
                        </span>
                        <button
                            @click="removeVenueConfirm(venue.id)"
                            class="hover:opacity-50"
                        >
                            <i class="fas fa-trash text-red-500"></i>
                        </button>
                    </li>

                    <div
                        :id="'remove-venue-confirm-' + venue.id"
                        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50"
                    >
                        <div class="p-2 bg-white rounded shadow-md">
                            <h1 class="p-2 text-lg font-medium">
                                Are you sure to remove {{ venue.name }} for

                                {{ user_venue.fname }}
                                {{ user_venue.lname }}?
                            </h1>

                            <div class="flex justify-end space-x-1 mt-2">
                                <button
                                    @click="removeVenueConfirm(venue.id)"
                                    class="px-4 py-2 border border-gray-300 text-gray-800 rounded hover:opacity-50"
                                >
                                    No
                                </button>
                                <button
                                    @click="
                                        userVenueDelete(user_venue.id, venue.id)
                                    "
                                    class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                >
                                    Yes
                                </button>
                            </div>
                        </div>
                    </div>
                </ul>
                <form
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                >
                    <div class="mt-2">
                        <input
                            type="hidden"
                            :value="user_venue.id"
                            name="user_id"
                        />
                        <label for="">Select a venue</label>
                        <select
                            name="venue_id"
                            class="border p-2 w-full cursor-pointer"
                        >
                            <option value=""></option>
                            <option
                                v-for="venuelist in venue_list"
                                :key="venuelist"
                                :value="venuelist.id"
                            >
                                {{ venuelist.name }} at {{ venuelist.building }}
                            </option>
                        </select>
                    </div>
                    <div class="flex p-2 justify-end mt-2">
                        <button
                            class="px-4 py-2 bg-green-500 text-green-100 hover:opacity-50 rounded"
                        >
                            Add Venue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="p-2">
        <table class="w-full table-auto border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th
                        class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700"
                    >
                        Name
                    </th>

                    <th
                        class="px-4 py-2 w-[200px] border-b text-left text-sm font-semibold text-gray-700"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="venueCoordinator in venueCoordinators"
                    :key="venueCoordinator.id"
                    class="hover:bg-gray-300 bg-gray-100 cursor-pointer"
                >
                    <td class="px-4 py-2 border-b text-sm text-gray-700">
                        {{ venueCoordinator.lname }},
                        {{ venueCoordinator.fname }}
                    </td>

                    <td
                        class="px-4 py-2 border-b text-sm text-gray-700 space-x-2"
                    >
                        <button
                            @click="userVenue(venueCoordinator.id)"
                            class="text-blue-500 hover:opacity-50"
                            title="Preview"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue3";

export default {
    data() {
        return {};
    },
    methods: {
        userVenueDelete(userId, venueId) {
            router.delete(`/venue-coordinators/${userId}/${venueId}`);
        },
        userVenue(userId) {
            router.get(`/venue-coordinators/${userId}`);
        },
        submitForm(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            router.post("/venue-coordinator/venue-add", formData);
        },
    },
};
</script>
