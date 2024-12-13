<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });
</script>

<template>
    <section class="p-2 w-full">
        <div class="flex flex-wrap justify-between gap-1">
            <div
                class="flex-grow basis-[50px] bg-white border-2 border-2 rounded-t-xl border-gray-500 w-full"
            >
                <header
                    class="rounded-t-lg text-xl font-medium bg-blue-400 text-blue-100 drop-shadow p-2 border-b-2 border-gray-500"
                >
                    Approved Venues
                </header>
                <section class="h-[35vh] overflow-y-auto">
                    <table
                        class="min-w-full table-auto bg-white border-collapse border border-gray-200"
                    >
                        <thead
                            class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                        >
                            <tr>
                                <th
                                    class="py-2 px-4 border-b text-left text-gray-700"
                                >
                                    Event
                                </th>
                                <th
                                    class="py-2 px-4 border-b text-left text-gray-700"
                                >
                                    Venue
                                </th>
                                <th
                                    class="py-2 px-4 border-b text-left text-gray-700"
                                >
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="venue in venuesApproved"
                                :key="venue"
                                class="hover:bg-gray-50 text-xs"
                            >
                                <td class="py-2 px-4 border-b text-gray-600">
                                    {{ venue.event_name }}
                                </td>
                                <td class="py-2 px-4 border-b text-gray-600">
                                    {{ venue.venue_name }}
                                </td>
                                <td class="py-2 px-4 border-b text-gray-600">
                                    {{ formatDate(venue.date_start) }}
                                    {{ formatTime(venue.time_start) }}
                                    -
                                    {{ formatDate(venue.date_end) }}
                                    {{ formatTime(venue.time_end) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
            <div class="flex-grow basis-[100px] space-y-2 h-full">
                <div
                    class="bg-white border-2 border-2 rounded-t-xl border-gray-500"
                >
                    <header
                        class="text-xl rounded-t-lg font-medium bg-blue-400 text-blue-100 drop-shadow p-2 border-b-2 border-gray-500"
                    >
                        Requested Venues
                    </header>
                    <section class="h-[35vh] overflow-y-auto">
                        <table
                            class="min-w-full bg-white border-collapse border border-gray-200"
                        >
                            <thead
                                class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                            >
                                <tr class="">
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Event
                                    </th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Venue
                                    </th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="venue in venuesRequested"
                                    :key="venue"
                                    class="last:rounded-b-xl hover:bg-gray-50 text-xs"
                                >
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ venue.event_name }}
                                    </td>
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ venue.venue_name }} at
                                        {{ venue.venue_building }}
                                    </td>
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ formatDate(venue.date_start) }}
                                        {{ formatTime(venue.time_start) }}
                                        -
                                        {{ formatDate(venue.date_end) }}
                                        {{ formatTime(venue.time_end) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
                <div
                    class="flex-grow basis-[100px] bg-white border-2 border-2 rounded-t-xl border-gray-500 h-full"
                >
                    <header
                        class="text-xl font-medium bg-blue-400 text-blue-100 rounded-t-lg drop-shadow p-2 border-b-2 border-gray-500"
                    >
                        Declined Venues
                    </header>
                    <section class="h-[35vh] overflow-y-auto">
                        <table
                            class="min-w-full table-auto bg-white border-collapse border border-gray-200"
                        >
                            <thead
                                class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                            >
                                <tr class="">
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Event
                                    </th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Venue
                                    </th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-gray-700"
                                    >
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="venue in venuesDeclined"
                                    :key="venue"
                                    class="hover:bg-gray-50 text-xs"
                                >
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ venue.event_name }}
                                    </td>
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ venue.venue_name }}
                                    </td>
                                    <td
                                        class="py-2 px-4 border-b text-gray-600"
                                    >
                                        {{ formatDate(venue.date_start) }}
                                        {{ formatTime(venue.time_start) }}
                                        -
                                        {{ formatDate(venue.date_end) }}
                                        {{ formatTime(venue.time_end) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {};
    },
    props: {
        user_role: {
            type: String,
        },
        venuesApproved: {
            type: Array,
        },
        venuesRequested: {
            type: Array,
        },
        venuesDeclined: {
            type: Array,
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
    },
};
</script>
