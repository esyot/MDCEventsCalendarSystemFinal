<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '../../../Layouts/GuestLayout.vue';

const form = useForm({
  user: '',
  password: ''
});

const submit = () => {
  form.post('/login');
};

defineOptions({ layout: GuestLayout });


const toggleLogIn = () => {
  document.getElementById('login').classList.add('hidden');
};

</script>
<template>

<div id="login" class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50 hidden">
<div class="bg-white flex w-[600px] justify-center shadow-lg">
<div class="flex p-4 w-full justify-center items-center">
<img src="/resources/css/logo.png" alt="" class="drop-shadow rounded-full w-[200px] h-[200px]">
</div>

<div class="flex flex-col bg-blue-900 shadow-md w-full">
        <button @click="toggleLogIn" class="flex justify-end text-2xl text-white mr-2 hover:text-gray-500">&times;</button>
    <h1 class="text-xl flex justify-center font-bold text-white">Log In</h1>
      <form @submit.prevent="submit">
        <div class="px-4">
        <div class="mt-2">
          <label for="username" class="text-white">Username</label>
          <input 
            type="text" 
            placeholder="Username"
            class="block p-2 border border-gray-300 rounded w-full" 
            name="user" 
            v-model="form.user"/>
        </div>

        <div class="mt-2 ">
          <label for="password" class="text-white">Password</label>
          <input 
            type="password" 
            placeholder="Password"
            class="block p-2 border border-gray-300 w-full rounded" 
            name="password" 
            v-model="form.password"/>

            </div>
      
         
        </div>
        <span v-if="form.errors.user" class="mt-2 items-center space-x-1 flex border border-red-500 mx-4 text-sm justify-center text-red-500">
                <i class="fa-solid fa-circle-exclamation"></i>
               <span>{{ form.errors.user }}</span></span> 

        <div class="p-4 flex justify-end">
          <button class="px-4 py-2 bg-blue-500 text-white hover:text-blue-900 hover:bg-blue-400 rounded" type="submit">Login</button>
        </div>

      </form>     
</div>
</div>
</div>


<div class="flex flex-grow p-6 space-x-2">
    <div class="flex flex-col space-y-2 w-full">
      <div class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl">
        <div class="border-b-2 border-gray-500 w-full p-4">
          <input
            type="month"
            v-model="currentMonth"
            class="bg-transparent bg-white rounded p-2 shadow-md font-medium text-xl focus:outline-none"
          />
        </div>

        <div class="w-full h-full rounded-lg">
          <div class="flex bg-blue-300 justify-between px-4 font-bold">
            <div>SUN</div>
            <div>MON</div>
            <div>TUE</div>
            <div>WED</div>
            <div>THU</div>
            <div>FRI</div>
            <div>SAT</div>
          </div>
          <div class="grid grid-cols-7 gap-1 p-1">
            <template v-for="day in daysInMonth" :key="day ? day : Math.random()">
              <div
                v-if="day"
                class="flex justify-center items-center h-16 border border-gray-400"
              >
                {{ day }}
              </div>
              <div
                v-else
                class="h-16 border border-gray-400"
              ></div>
            </template>
          </div>
        </div>
      </div>

      <div class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500">
        <div class="flex flex-col p-4 border-b-2 border-gray-500">
          <h1 class="text-2xl font-medium">Today's Event</h1>
        </div>
        <div class="flex flex-col overflow-y-auto my-2">
          <div
            v-for="(event, index) in events_today"
            :key="index"
            class="flex items-center shadow-md mx-2"
          >
            <div class="ml-6">
              <p class="font-bold">{{ event.title }}</p>
              <span>{{ event.date }} at {{ formatTime(event.time_start) }} - {{ formatTime(event.time_end) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500">
      <div class="p-4 border-b-2 border-gray-500">
        <h1 class="text-2xl font-medium">Event Updates</h1>
      </div>
      <div class="flex flex-col space-y-2">
        <div class="flex flex-col items-center justify-center mx-4 my-2 space-y-2">
          <div
            v-for="(event, index) in events"
            :key="index"
            title="Click to preview"
            @click="preview(event)"
            class="flex items-center p-2 shadow-md w-[400px] justify-center hover:opacity-50 cursor-pointer transition-transform duration-300 ease-in-out hover:scale-90"
          >
            <i class="fa-regular fa-bell fa-light fa-2xl"></i>
            <div class="ml-6">
              <p class="font-bold">{{ event.name }}</p>
              <span>{{ event.date }} at {{ formatTime(event.time_start) }} - {{ formatTime(event.time_end) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    v-if="isModalOpen"
    class="flex fixed inset-0 bg-gray-800 justify-center items-center bg-opacity-50"
  >
    <div class="bg-white p-4 rounded">
      <p><strong>Title:</strong> {{ selectedEvent.name }}</p>
      <p><strong>Date Start:</strong> {{ selectedEvent.date }}</p>
      <p><strong>Time Start:</strong> {{ formatTime(selectedEvent.time_start) }}</p>
      <p><strong>Time End:</strong> {{ formatTime(selectedEvent.time_end) }}</p>
      <div class="flex justify-end">
        <button
          @click="closeModal"
          class="mt-4 p-2 bg-blue-500 text-white rounded"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    events: {
      type: Array,
      default: () => [],
    },
    events_today: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      currentMonth: '',
      isModalOpen: false,
      selectedEvent: null,
    };
  },
  computed: {
    daysInMonth() {
      const [year, month] = this.currentMonth.split("-").map(Number);
      const date = new Date(year, month - 1, 1);
      const days = [];
      const firstDay = date.getDay();
      const lastDate = new Date(year, month, 0).getDate();

      for (let i = 0; i < firstDay; i++) {
        days.push(null);
      }

      for (let i = 1; i <= lastDate; i++) {
        days.push(i);
      }

      return days;
    },
  },
  methods: {
    formatTime(time) {
      const [hours, minutes] = time.split(":");
      const formattedHours = hours % 12 || 12;
      const ampm = hours < 12 ? "am" : "pm";
      return `${formattedHours}:${minutes} ${ampm}`;
    },
    preview(event) {
      this.selectedEvent = event;
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.selectedEvent = null;
    },
  },
  mounted() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    this.currentMonth = `${year}-${month}`;
  },
};
</script>
