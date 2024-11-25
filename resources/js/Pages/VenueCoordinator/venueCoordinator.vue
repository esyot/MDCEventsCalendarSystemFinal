<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });

// Define props for the component
defineProps({
    venueCoordinators: {
        type: Array,
        required: true, // Add this if you want to ensure the prop is required
    },
});
</script>

<template>
    <div class="p-2">
        <table
            class="min-w-full table-auto border-collapse border border-gray-200"
        >
            <thead>
                <tr class="bg-gray-100">
                    <th
                        class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700"
                    >
                        Name
                    </th>
                    <th
                        class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700"
                    >
                        Department
                    </th>

                    <th
                        class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="venueCoordinator in venueCoordinators"
                    :key="venueCoordinator.id"
                    class="hover:bg-gray-50 cursor-pointer"
                >
                    <td class="px-4 py-2 border-b text-sm text-gray-700">
                        {{ venueCoordinator.user.teacher.name }}
                    </td>

                    <td class="px-4 py-2 border-b text-sm text-gray-700">
                        {{ venueCoordinator.user.teacher.department.name }}
                    </td>
                    <!-- <td class="px-4 py-2 border-b text-sm text-gray-700">
                        <div>
                            {{ venueCoordinator.venue.name }} at
                            {{ venueCoordinator.venue.building }}
                        </div>
                    </td> -->

                    <td
                        class="px-4 py-2 border-b text-sm text-gray-700 space-x-2"
                    >
                        <button
                            class="text-blue-500 hover:opacity-50"
                            title="Preview"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                        <button
                            class="text-yellow-500 hover:opacity-50"
                            title="Edit"
                        >
                            <i class="fas fa-pencil"></i>
                        </button>

                        <button
                            class="text-red-500 hover:opacity-50"
                            title="Remove"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
