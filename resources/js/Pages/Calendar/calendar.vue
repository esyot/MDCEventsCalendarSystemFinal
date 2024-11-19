<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import MainLayout from '../../Layouts/MainLayout.vue';
import { Inertia } from '@inertiajs/inertia';


defineOptions({ layout: MainLayout });


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

// Get the current year and current date
const currentDate = new Date();
const currentYear = ref(currentDate.getFullYear());
const currentMonth = ref(currentDate.getMonth() + 1); // Months are zero-indexed
const currentDay = ref(currentDate.getDate());

// Check if a day is today's date
const isToday = (month, day) => {
  return currentYear.value === currentDate.getFullYear() &&
         month === currentMonth.value &&
         day === currentDay.value;
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

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Function to update calendar data when the year changes
const updateCalendarDataForYear = () => {
  calendarData.value = monthsInYear.map((month) => {
    const days = daysInMonth(month, currentYear.value);
    const firstDay = getFirstDayOfMonth(month, currentYear.value);
    const monthDays = Array.from({ length: days }, (_, index) => index + 1);

    return Array(firstDay).fill(null).concat(monthDays);
  });
};

const changeYear = (direction) => {
  if (direction === 'previous' && currentYear.value > 2020) {
    currentYear.value--;
  } else if (direction === 'next' && currentYear.value < 2030) {
    currentYear.value++;
  }
  updateCalendarDataForYear();
};

const selectedMonth = ref(null);
const searchQuery = ref('');
const selectedLevel = ref('');
const selectedDepartment = ref('');
const levels = ['College', 'Elementary', 'Graduate School', 'High School'];

const selectMonth = (monthIndex) => {
  selectedMonth.value = monthIndex;
};

const backToYearView = () => {
  selectedMonth.value = null;
};


</script>

<template>
<div class="flex flex-col ">
    <div class="flex flex-col p-2 space-x-2">
      <div class="items-center flex space-x-2 p-2">
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
        <div>
                        
                          <select type="text" class="block p-2 border border-gray-300 w-full rounded">
                                    <option v-for="department in departments" :key="department" :value="department.id">{{  department.name}}</option>
                                </select>
                        </div>
      
      </div>
    <div class="">
     
      <div class="flex flex-col bg-gray-100 border-2 border-gray-300 rounded-xl ">
        <div class=" w-full px-4 py-2 flex justify-between items-center border-b ">
          <button @click="changeYear('previous')" class="">
          <i class="fas fa-chevron-circle-left text-blue-500 fa-2xl hover:opacity-50"></i>
          </button>
          <select
    v-model="currentYear"
    class="px-4 py-2 shadow-inner border focus:outline-none"
    @change="updateCalendarDataForYear"
  >
    <option v-for="year in Array.from({ length: 11 }, (_, i) => i + 2020)" :key="year" :value="year">
      {{ year }}
    </option>
  </select>
          <button @click="changeYear('next')" class="">
          <i class="fas fa-chevron-circle-right text-blue-500 fa-2xl hover:opacity-50"></i>
          </button>
        </div>

  <div v-if="selectedMonth === null" class="w-full rounded-lg overflow-y-auto h-[70vh]">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 p-2">
      <template v-for="(month, monthIndex) in calendarData" :key="monthIndex" class="">
        <div class="transition-transform duration-300 ease-in-out hover:scale-90 flex flex-col border border-gray-300 drop-shadow p-2 hover:bg-gray-200 cursor-pointer" @click="selectMonth(monthIndex)">
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
              <div
                v-if="day"
                :class="[
                  'flex justify-center items-center h-6 text-xs',
                  { 'bg-blue-300 font-bold': isToday(monthIndex + 1, day) }
                ]"
              >
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
          <div class="">
            <div class="bg-gradient-to-r from-blue-500 to-blue-800">
              <div class="flex items-center justify-between px-2 space-x-2">
                 
                   <span class="text-blue-100 text-2xl">{{ ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'][selectedMonth] }}
            </span>
            <button @click="backToYearView()" class=" flex justify-center px-4 py-2  text-blue-100 hover:opacity-50 rounded">
                  back
                  </button>
           
              </div>
            </div>
            <div class="">

              <div class="grid grid-cols-7">
                <template v-for="dayOfWeek in daysOfWeek" :key="dayOfWeek" class="flex justify-between">
                <div :class="dayOfWeek=='Sun' ? 'text-red-500' : ''" class="text-center font-bold bg-gray-300">
                 {{ dayOfWeek }}
                </div>
              </template>
              </div>
            
              <div class="grid grid-cols-7 h-[60vh]">
              <template v-for="day in calendarData[selectedMonth]" :key="day" class="flex">
                
                <button 
    class="text-lg" 
    v-if="day" 
    @click="openCreateEventModal(day)" 
    :disabled="user_role_calendar === 'venue_coordinator'"
    :class="[
        'flex justify-center items-center hover:bg-gray-300 px-2 transition-transform duration-300 ease-in-out hover:scale-90',
        { 'bg-blue-300 font-bold': isToday(selectedMonth + 1, day) }
    ]">
    {{ day }}
</button>





                
                <div v-else class=""></div>

                <div :id="'create-event-modal-' + day" class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden">
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="bg-white p-2 w-[500px] shadow-md rounded">
      <div class="flex w-full justify-between mb-4">
                            <h1 class="text-2xl">Create Event on {{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][selectedMonth] }} {{ day }}, {{ currentYear  }}</h1>

                            <button type="button" @click="closeCreateEventModal(day)" class="text-2xl font-bold hover:opacity-50">&times;</button>
                        </div>
                        <div>
  <input type="date" id="event_date" class="hidden" :value="convertToDate(selectedMonth+1, day, currentYear)" name="event_date">
</div>

                        <div>
                            <label for="">Event Title:</label>
                            <input type="text" name="event_name" id="" placeholder="Input event title" class="w-full block p-2 border border-gray-300 rounded shadow-inner" required>
                            
                           </div>

                           <div>
                            <label for="">Term:</label>
                            <select name="event_term_id" id="" class="w-full block p-2 border border-gray-300 rounded shadow-inner" required>
                              <option v-for="term in terms" :key="term" :value="term.id">{{term.name}}</option>
                            </select>
                           </div>

  
                        <div>
                            <label for="">Venue:</label>
                            <select type="text" name="event_venue" class="block p-2 border border-gray-300 w-full rounded" required>
                                    <option v-for="venue in venues" :key="venue" :value="venue.id">{{ venue.name}}</option>
                                </select>
                        </div>
                        <div>
                            <label for="">Time:</label>
                            <div class="flex items-center space-x-2">
                                <input type="time" name="event_time_start" id="" class="block p-2 border border-gray-300 w-full rounded" required>
                            <h1>To</h1>
                            <input type="time" name="event_time_end" id="" class="block p-2 border border-gray-300 w-full rounded">
                            </div>
                          
                        </div>

                        <div>
                          <label for="">Departments:</label>
                          <select type="text" name="event_department_id" class="block p-2 border border-gray-300 w-full rounded" required>
                                    <option v-for="department in departments" :key="department" :value="department.id">{{  department.name}}</option>
                                </select>
                        </div>

                        <div>
                          <label for="">Levels</label>
                          <select name="event_levels" id="" class="block p-2 border border-gray-300 rounded shadow-inner" required> 
                          <option value="['1']">1</option>
                          <option value="['2']" id="">2</option>
                          <option value="['3']" id="">3</option>
                          <option value="['4']" id="">4</option>
                          <option value="['1', '2']" id="">1-2</option>
                          <option value="['1','2', '3']" id="">1-3</option>
                          <option value="['1', '2', '3', '4']" id="">1-4</option>
                          <option value="['2', '3']" id="">2-3</option>
                          <option value="['2', '3', '4']" id="">2-4</option>
                          <option value="['3', '4']" id="">3-4</option>
                        
                          </select>

                        </div>
                       
                        <div>
                            <label for="">Activity Design:</label>
                            <input type="file" name="activity_design" class="block p-2 border border-gray-300 w-full rounded">
                        </div>
                       
                        <div class="flex justify-end p-2">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded">Submit</button>
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


  <div v-if="success" class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 z-50">
    <div class="bg-white p-2">
{{success}}
    </div>
  </div>
</template>

<script>

  import axios from 'axios';

const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


const convertToDate = (month, day, year) => {
 
  const date = new Date(year, month - 1, day); // Month is 0-based, so subtract 1

  // Extract year, month, and day, ensuring they are in the correct format
  const formattedYear = date.getFullYear();
  const formattedMonth = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const formattedDay = String(date.getDate()).padStart(2, '0'); // Add leading zero if necessary

  // Return formatted string in the format YYYY-MM-DD
  return `${formattedYear}-${formattedMonth}-${formattedDay}`;
  
}
export default {

  props: {
   departments: {
    type: Array
   },
    venues: {
      type: Array,
      required: true
    } ,
    events: {
      type: Array,

    },
    terms: {
      type: Array,
    },
    user_role_calendar: {
      type: String,
    },

  },
  methods: {
    submitForm(event) {
      const formData = new FormData(event.target);

      Inertia.post('/admin/event-create', formData, {
        preserveState: true, 
      
      });
    },
  }
};


const openCreateEventModal = (day) => {

    document.getElementById('create-event-modal-'+day).classList.remove('hidden');
}

const closeCreateEventModal = (day) => {

document.getElementById('create-event-modal-'+day).classList.add('hidden');
}
</script>