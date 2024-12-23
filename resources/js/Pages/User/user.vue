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
    departments: {
        type: Array,
    },
    selectedUser: {
        nullable: true,
    },
    selectedUserDepartments: {
        type: Array,
        nullable: true,
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

const modalSelectedUserClose = () => {
    document.getElementById("modalSelectedUser").classList.add("hidden");
};
</script>

<template>
    <div
        id="modalSelectedUser"
        v-if="selectedUser != null"
        class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white">
            <div class="px-2 text-xl">
                <h1>
                    Assigned Department of {{ selectedUser.lname }},
                    {{ selectedUser.fname }}
                </h1>
            </div>

            <div class="p-2">
                <h1>Assigned Deparments:</h1>
                <ul>
                    <li
                        class="p-2 border flex justify-between items-center"
                        v-for="department in selectedUserDepartments"
                        :key="department"
                    >
                        <span> {{ department.name }}</span>
                        <button
                            @click="
                                removeUserDepartment(
                                    selectedUser.id,
                                    department.id
                                )
                            "
                        >
                            <i
                                class="fas fa-trash text-red-500 hover:opacity-50"
                            ></i>
                        </button>
                    </li>
                </ul>
            </div>
            <form action="/users/user-add-department" method="GET">
                <input type="hidden" name="user" :value="selectedUser.id" />
                <div class="p-2">
                    <select
                        class="block p-2 border border-gray-300 rounded w-full"
                        name="department"
                        id=""
                    >
                        <option
                            v-for="department in departments"
                            :key="department"
                            :value="department.id"
                        >
                            {{ department.name }}
                        </option>
                    </select>
                </div>
                <div class="flex justify-end p-2 space-x-1">
                    <button
                        @click="modalSelectedUserClose()"
                        type="button"
                        class="px-4 py-2 border border-gray-300 text-gray-800 hover:opacity-50 rounded"
                    >
                        Close
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-green-500 text-green-100 hover:opacity-50 rounded"
                    >
                        Add Department
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- /selected User -->

    <div
        id="searchUserForm"
        class="flex fixed inset-0 items-center justify-center bg-gray-800 bg-opacity-50 z-50"
    >
        <div class="bg-white rounded">
            <div class="flex justify-between">
                <h1 class="text-xl font-medium p-2">Search User</h1>
                <button
                    @click="toggleSearchUserFormModal()"
                    class="font-bold text-2xl px-2 hover:opacity-50"
                >
                    &times;
                </button>
            </div>

            <div class="p-2">
                <form @submit.prevent="userSearch" method="POST" class="flex">
                    <div
                        class="p-2 space-x-1 border border-gray-300 w-full rounded-l-full shadow-inner"
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
                        class="text-blue-100 hover:opacity-50 rounded-r bg-blue-500"
                    >
                        <i class="fas fa-paper-plane text-sm px-3"></i>
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
                                <form
                                    @submit.prevent="userAddRole"
                                    method="POST"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        :value="user.id"
                                    />

                                    <div>
                                        <label class="font-medium"
                                            >Department:</label
                                        >
                                        <select
                                            name="department"
                                            id=""
                                            class="block p-2 border border-gray-300 w-full rounded"
                                        >
                                            <option
                                                v-for="department in departments"
                                                :key="department"
                                                :value="department.id"
                                            >
                                                {{ department.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <label class="font-medium"
                                        >Select Role</label
                                    >

                                    <div>
                                        <input
                                            type="radio"
                                            name="role"
                                            id="event-coordinator"
                                            value="event_coordinator"
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
                                            value="venue_coordinator"
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
                placeholder="Search user"
                class="bg-transparent focus:outline-none"
            />
        </div>
        <button
            v-if="user_role == 'admin' || user_role == 'sec-admin'"
            @click="toggleSearchUserFormModal()"
            class="bg-blue-500 text-blue-100 py-2 px-4 rounded hover:opacity-90"
        >
            <span>Add User's role</span>
        </button>
    </div>

    <div class="overflow-x-auto mx-2 shadow-lg shadow-gray-300">
        <div class="max-h-[560px] overflow-y-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead
                    class="sticky top-0 bg-gray-100 text-gray-600 uppercase text-sm leading-normal"
                >
                    <tr>
                        <th class="py-3 px-6 text-center">Last Name</th>
                        <th class="py-3 px-6 text-center">First Name</th>
                        <th class="py-3 px-6 text-center">Role</th>
                        <th
                            class="text-center py-3"
                            v-if="
                                user_role == 'admin' || user_role == 'sec-admin'
                            "
                        >
                            Edit Role
                        </th>
                        <th class="py-3 text-center">Assign Department</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light overflow-y-auto">
                    <tr
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="hover:bg-gray-200"
                    >
                        <td class="py-3 text-center px-6">{{ user.lname }}</td>
                        <td class="py-3 text-center px-6">{{ user.fname }}</td>
                        <td class="py-3 text-center px-6">
                            {{ user.role_name }}
                        </td>
                        <td
                            class="py-3 px-6 flex items-center justify-center space-x-4"
                            v-if="
                                user_role == 'admin' || user_role == 'sec-admin'
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
                        <td class="py-3 text-center">
                            <button
                                @click="navigateTo(user.user_id)"
                                class="px-4 py-2 text-blue-100 rounded hover:opacity-50 bg-blue-500"
                            >
                                View Department
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- loop -->

            <div v-for="user in filteredUsers" :key="user.id">
                <div
                    v-if="modalEditRoleId === user.id"
                    class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50"
                >
                    <div class="bg-white rounded-lg w-[500px] p-4">
                        <div class="flex items-center mb-4">
                            <h1 class="text-xl font-semibold text-gray-700">
                                Edit role for
                            </h1>
                            <span
                                class="ml-2 text-lg font-medium text-gray-900"
                            >
                                {{ user.lname }}, {{ user.fname }}
                            </span>
                        </div>

                        <form @submit.prevent="userRoleUpdate" method="POST">
                            <input
                                type="hidden"
                                name="user"
                                :value="user.user_id"
                            />

                            <div class="mt-2">
                                <label for="" class="font-bold"
                                    >Select Role:</label
                                >

                                <input
                                    type="hidden"
                                    :value="currentRole"
                                    name="currentRole"
                                />

                                <div
                                    v-for="role in rolesList"
                                    :key="role"
                                    class="mb-3"
                                >
                                    <label
                                        class="flex items-center space-x-2 text-gray-700"
                                    >
                                        <input
                                            type="radio"
                                            :value="role"
                                            name="role"
                                            v-model="currentRole"
                                            class="form-radio text-blue-500"
                                        />
                                        <span>{{ role }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-1 p-2 mt-4">
                                <button
                                    type="button"
                                    @click="modalEditRoleId = null"
                                    class="px-4 py-2 bg-red-500 text-red-100 hover:opacity-50 rounded"
                                >
                                    Close
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                >
                                    Save Role
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
                            <h1 class="text-xl">
                                Are you sure to remove role for this user?
                            </h1>
                            <div class="flex justify-end p-2 space-x-1">
                                <button
                                    type="button"
                                    @click="closeDeleteModal"
                                    class="px-4 py-2 bg-gray-500 text-gray-100 hover:opacity-50 rounded"
                                >
                                    No
                                </button>
                                <button
                                    @click="deleteUserRole(user.user_id)"
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded"
                                >
                                    Yes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue3";

export default {
    data() {
        return {
            searchQuery: "",
        };
    },
    methods: {
        navigateTo(userId) {
            router.get(`/users?user=${userId}`);
        },

        userAddRole(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            router.post("/user-add-role", formData);
        },
        userSearch(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            router.post("/users", formData);
        },
        removeUserDepartment(userId, departmentId) {
            router.delete(
                `/users/user-remove-department/${userId}/${departmentId}`
            );
        },
        userRoleUpdate(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            router.post("/user-role-update", formData);
        },
        deleteUserRole(userId) {
            router.delete(`/user-delete-role/${userId}`);
        },
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
