<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '../../../Layouts/GuestLayout.vue';

defineOptions({ layout: GuestLayout });


const form = useForm({
  user: '',
  password: ''
});

const submit = () => {
  form.post('/login');
};

const toggleLogIn = () => {
  document.getElementById('login').classList.add('hidden');
};




// Get the current year and current date using JavaScript's Date object
const currentDate = new Date();
const currentYear = ref(currentDate.getFullYear());

// Create an array for months
const monthsInYear = Array.from({ length: 12 }, (_, index) => index + 1);

// Generate days for each month with the correct starting day of the week
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

    // Add empty slots for days before the first day of the month
    return Array(firstDay).fill(null).concat(monthDays);
  })
);

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const changeYear = (direction) => {
  if (direction === 'previous' && currentYear.value > 2020) {
    currentYear.value--;
  } else if (direction === 'next' && currentYear.value < 2030) {
    currentYear.value++;
  }
  calendarData.value = monthsInYear.map((month) => {
    const days = daysInMonth(month, currentYear.value);
    const firstDay = getFirstDayOfMonth(month, currentYear.value);
    const monthDays = Array.from({ length: days }, (_, index) => index + 1);

    // Add empty slots for days before the first day of the month
    return Array(firstDay).fill(null).concat(monthDays);
  });
};

const selectedMonth = ref(null);
const searchQuery = ref('');
const selectedLevel = ref('');
const selectedDepartment = ref('');
const levels = ['College','Elementary',  'Graduate School', 'High School'];
const departments = ['CABM-B', 'CABM-H', 'CAST', 'CCJ','COE', 'CON'];

const selectMonth = (monthIndex) => {
  selectedMonth.value = monthIndex;
};

const backToYearView = () => {
  selectedMonth.value = null;
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
      <div class="flex flex-row space-x-4 mb-4">
        <input
          v-model="searchQuery"
          placeholder="Search"
          class="p-2 border rounded shadow-sm focus:outline-none"
        />
        <select v-model="selectedLevel" class="p-2 border rounded">
          <option value="">Level</option>
          <option v-for="level in levels" :key="level" :value="level">
            {{ level }}
          </option>
        </select>
        <select v-model="selectedDepartment" class="p-2 border rounded">
          <option value="">Department</option>
          <option v-for="dept in departments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
      </div>
      <div class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl">
        <div class="border-b-2 border-gray-500 w-full p-4 flex justify-between items-center">
          <button @click="changeYear('previous')" class="p-2 bg-gray-300 rounded shadow-md">Previous</button>
          <input
            v-model="currentYear"
            min="2020"
            max="2030"
            class="bg-transparent bg-white rounded p-2 shadow-md font-medium text-xl focus:outline-none text-center w-24"
            @change="calendarData.value = monthsInYear.map(month => {
              const days = daysInMonth(month, currentYear.value);
              const firstDay = getFirstDayOfMonth(month, currentYear.value);
              const monthDays = Array.from({ length: days }, (_, index) => index + 1);
              return Array(firstDay).fill(null).concat(monthDays);
            })"
          />
          <button @click="changeYear('next')" class="p-2 bg-gray-300 rounded shadow-md">Next</button>
        </div>

        <div v-if="selectedMonth === null" class="w-full h-full rounded-lg">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template v-for="(month, monthIndex) in calendarData" :key="monthIndex">
              <div class="flex flex-col border border-gray-400 p-2 hover:bg-gray-200 cursor-pointer" @click="selectMonth(monthIndex)">
                <div class="bg-blue-300 p-1 font-bold text-center">
                  {{ ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'][monthIndex] }}
                </div>
                <div class="grid grid-cols-7 gap-1 p-1">
                  <template v-for="dayOfWeek in daysOfWeek" :key="dayOfWeek">
                    <div class="flex justify-center items-center font-bold h-6 text-xs">
                      {{ dayOfWeek }}
                    </div>
                  </template>
                  <template v-for="day in month" :key="day">
                    <div v-if="day" class="flex justify-center items-center h-6 text-xs">
                      {{ day }}
                    </div>
                    <div v-else class="h-6"></div>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div v-else class="w-full h-full rounded-lg">
          <button @click="backToYearView" class="mb-2 p-2 bg-gray-300 rounded shadow-md">Back</button>
          <div class="flex flex-col border border-gray-400">
            <div class="bg-blue-300 p-2 font-bold text-center">
              {{ ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'][selectedMonth] }}
            </div>
            <div class="grid grid-cols-7 gap-1 p-1">
              <template v-for="dayOfWeek in daysOfWeek" :key="dayOfWeek">
                <div class="flex justify-center items-center font-bold h-8 border-b border-gray-300">
                  {{ dayOfWeek }}
                </div>
              </template>
              <template v-for="day in calendarData[selectedMonth]" :key="day">
                <div v-if="day" class="flex justify-center items-center h-8 border-t border-gray-200">
                  {{ day }}
                </div>
                <div v-else class="h-8"></div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>