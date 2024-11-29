<script setup>
import { onMounted, ref, watch } from "vue";
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    user_searched: {
        type: Array,
    },
    roles: {
        type: Array,
    },
    user_role: {
        type: String,
    },
});

const search_value = ref("");
const modalDeleteRoleId = ref(null);
const modalEditRoleId = ref(null);
const rolesList = ref([]);
const currentRole = ref(null);

const searchUserForm = (search_value) => {
    const formElement = document.getElementById("searchUserForm");
    if (!search_value || search_value.trim() === "") {
        formElement.classList.add("hidden");
    } else {
        formElement.classList.remove("hidden");
    }
};

const toggleDropdown = (userId) => {
    const dropdown = document.getElementById("role-dropdown-" + userId);
    const arrow = document.getElementById("arrow-" + userId);

    if (dropdown) {
        dropdown.classList.toggle("hidden");
        arrow.classList.toggle(
            "fa-chevron-up",
            !dropdown.classList.contains("hidden")
        );
        arrow.classList.toggle(
            "fa-chevron-down",
            dropdown.classList.contains("hidden")
        );
    }
};

const toggleSearchUserFormModal = () => {
    document.getElementById("searchUserForm").classList.toggle("hidden");
};

const closeSearchFormModal = () => {
    document.getElementById("searchUserForm").classList.add("hidden");
};

const openDeleteModal = (userId) => {
    modalDeleteRoleId.value = userId;
};

const closeDeleteModal = () => {
    modalDeleteRoleId.value = null;
};

const openEditRoleModal = (userId) => {
    modalEditRoleId.value = userId;
    combineCurrentRoleWithRoles(userId);
};

const closeEditRoleModal = () => {
    modalEditRoleId.value = null;
    currentRole.value = null;
};

const combineCurrentRoleWithRoles = (userId) => {
    const user = props.users.find((u) => u.id === userId);
    if (!user) return;

    currentRole.value = user.role_name;

    rolesList.value = [...props.roles];

    if (!rolesList.value.includes(currentRole.value)) {
        rolesList.value.unshift(currentRole.value);
    }
};

const saveRole = () => {
    closeEditRoleModal();
};

watch(search_value, (newValue) => {
    searchUserForm(newValue);
});

onMounted(() => {
    const queryParams = new URLSearchParams(window.location.search);
    search_value.value = queryParams.get("search_value") || "";
    searchUserForm(search_value.value);

    if (search_value.value) {
        toggleSearchUserFormModal();
    }
});
</script>

<template>
    <div
        id="searchUserForm"
        class="flex fixed inset-0 items-center justify-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white p-2 rounded">
            <div class="flex justify-between">
                <h1 class="text-xl font-medium p-2">Search User</h1>
                <button
                    @click="toggleSearchUserFormModal()"
                    class="font-bold text-2xl hover:opacity-50"
                >
                    &times;
                </button>
            </div>

            <div>
                <form
                    value="{{ search_value }}"
                    action="/user-search"
                    method="GET"
                    class="flex space-x-2"
                >
                    <div
                        class="p-2 space-x-1 border border-gray-300 w-full rounded-full shadow-inner"
                    >
                        <i class="fas fa-magnifying-glass"></i>
                        <input
                            name="search_value"
                            type="text"
                            class="focus:outline-none"
                            placeholder="Search user"
                            required
                        />
                    </div>
                    <button
                        class="px-2.5 text-blue-100 hover:opacity-50 py-2 rounded bg-blue-500"
                    >
                        Search
                    </button>
                </form>
                <div class="h-64 flex flex-col overflow-y-auto">
                    <div
                        v-if="user_searched.length == 0"
                        class="flex relative fixed inset-0 items-center justify-center p-2"
                    >
                        <h1>No results found!</h1>
                    </div>
                    <div
                        class="flex flex-col justify-start p-2 border border-gray-300 m-1 cursor-pointer"
                        v-for="user in user_searched"
                        :key="user.id"
                    >
                        <div>
                            <div class="flex justify-between">
                                <span
                                    >Name:
                                    {{ user.lname }}
                                    {{ user.fname }}
                                </span>
                                <button @click="toggleDropdown(user.id)">
                                    <i
                                        :id="'arrow-' + user.id"
                                        class="fas fa-chevron-down"
                                    ></i>
                                </button>
                            </div>

                            <div
                                :id="'role-dropdown-' + user.id"
                                class="flex flex-col hidden"
                            >
                                <form action="/user-add-role" method="GET">
                                    <input
                                        type="hidden"
                                        name="id"
                                        :value="user.id"
                                    />
                                    <h1 class="font-medium">Select Role</h1>
                                    <div>
                                        <input
                                            type="radio"
                                            name="role"
                                            id="superadmin"
                                            value="superadmin"
                                        />
                                        <label for="superadmin"
                                            >Superadmin</label
                                        >
                                    </div>
                                    <div>
                                        <input
                                            type="radio"
                                            name="role"
                                            id="admin"
                                            value="admin"
                                        />
                                        <label for="admin">Admin</label>
                                    </div>

                                    <div>
                                        <input
                                            type="radio"
                                            name="role"
                                            id="event-coordinator"
                                            value="event-coordinator"
                                        />
                                        <label for="event-coordinator"
                                            >Event Coordinator</label
                                        >
                                    </div>
                                    <div>
                                        <input
                                            type="radio"
                                            name="role"
                                            id="venue-coordinator"
                                            value="venue-coordinator"
                                        />
                                        <label for="venue-coordinator"
                                            >Venue Coordinator</label
                                        >
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                        >
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between w-full p-2">
        <div class="p-2 bg-white space-x-1 rounded-xl border border-gray-300">
            <i class="fas fa-magnifying-glass"></i>
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search..."
                class="bg-transparent focus:outline-none"
            />
        </div>
        <button
            v-if="user_role == 'super_admin'"
            @click="toggleSearchUserFormModal()"
            class="bg-green-500 text-white py-2 px-4 rounded hover:opacity-90"
        >
            <span>Add User's role</span>
        </button>
    </div>

    <div class="overflow-x-auto mx-4 shadow-lg shadow-gray-300">
        <div class="max-h-[560px] overflow-y-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead
                    class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                >
                    <tr>
                        <th class="py-3 px-6 text-left">Last Name</th>
                        <th class="py-3 px-6 text-left">First Name</th>
                        <th class="py-3 px-6 text-left">Role</th>
                        <th
                            class="text-left py-3"
                            v-if="
                                user_role == 'super_admin' ||
                                user_role == 'admin'
                            "
                        >
                            Edit Role
                        </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light overflow-y-auto">
                    <tr
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="hover:bg-gray-200"
                    >
                        <td class="py-3 px-6">{{ user.lname }}</td>
                        <td class="py-3 px-6">{{ user.fname }}</td>
                        <td class="py-3 px-6">{{ user.role_name }}</td>
                        <td
                            class="py-3 px-6 flex items-center space-x-4"
                            v-if="
                                user_role == 'super_admin' ||
                                user_role == 'admin'
                            "
                        >
                            <button
                                @click="openEditRoleModal(user.id)"
                                class="hover:opacity-50"
                            >
                                <i class="fas fa-pencil text-yellow-500"></i>
                            </button>

                            <button
                                @click="openDeleteModal(user.id)"
                                type="button"
                                class="text-red-500 hover:opacity-50"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>

                        <div
                            v-if="modalEditRoleId === user.id"
                            class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50"
                        >
                            <div class="bg-white rounded w-[500px]">
                                <form action="/user-role-update" method="GET">
                                    <div class="flex items-center">
                                        <h1 class="p-2">Edit Role for</h1>
                                        <span class="font-medium">
                                            {{ user.lname }},
                                            {{ user.fname }}
                                        </span>
                                    </div>

                                    <div>
                                        <input
                                            type="hidden"
                                            :value="user.id"
                                            name="id"
                                        />
                                        <input
                                            type="hidden"
                                            :value="currentRole"
                                            name="currentRole"
                                        />

                                        <div
                                            v-for="role in rolesList"
                                            :key="role"
                                            class="p-2"
                                        >
                                            <label>
                                                <input
                                                    type="radio"
                                                    name="role"
                                                    :value="role"
                                                    v-model="currentRole"
                                                />
                                                {{ role }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex justify-between p-2">
                                        <button
                                            type="button"
                                            @click="modalEditRoleId = null"
                                            class="px-4 py-2 bg-red-500 text-white rounded"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                        >
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div
                            v-if="modalDeleteRoleId === user.id"
                            class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50"
                        >
                            <div class="bg-white rounded">
                                <div class="p-2">
                                    <form
                                        :action="'/user-delete-role/' + user.id"
                                        method="GET"
                                    >
                                        <h1 class="text-xl">
                                            Are you sure to remove role for this
                                            user?
                                        </h1>
                                        <div
                                            class="flex justify-end p-2 space-x-1"
                                        >
                                            <button
                                                type="button"
                                                @click="closeDeleteModal"
                                                class="px-4 py-2 bg-gray-500 text-gray-100 hover:opacity-50 rounded"
                                            >
                                                No
                                            </button>
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                            >
                                                Yes
                                            </button>
                                        </div>
                                    </form>
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
export default {
    data() {
        return {
            searchQuery: "",
        };
    },
    computed: {
        filteredUsers() {
            if (!this.searchQuery) {
                return this.users;
            }

            return this.users.filter((user) => {
                const searchQueryLower = this.searchQuery.toLowerCase();
                return (
                    user.lname.toLowerCase().includes(searchQueryLower) ||
                    user.fname.toLowerCase().includes(searchQueryLower) ||
                    user.role_name.toLowerCase().includes(searchQueryLower)
                );
            });
        },
    },
};
</script>
