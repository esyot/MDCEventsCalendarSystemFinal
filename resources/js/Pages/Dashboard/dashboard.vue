<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';

defineOptions({ layout: MainLayout});
</script>

<template>
    <div class="flex p-2 space-x-4 w-full p-4">
        <div class="flex flex-col space-y-4">
                <div class="flex flex-col bg-gray-100 border-2 border-gray-500 rounded-xl w-[700px]">

<div class="border-b-2 border-gray-500 w-full p-4">
    <input type="month" v-model="currentMonth" class="bg-transparent text-xl focus:outline-none" />
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
        <template v-for="day in daysInMonth">
            <div v-if="day" class="flex justify-center items-center h-16 border border-gray-400">
                {{ day }}
            </div>
            <div v-else class="h-16 border border-gray-400"></div>
        </template>
    </div>
</div>
</div>
        <div class="flex flex-col bg-gray-100 rounded-xl border-2 border-gray-500">
                <div class="flex flex-col p-4 border-b-2 border-gray-500 h-full">
                        <h1 class="text-2xl font-medium">Today's Event</h1>
                </div>
                <div class="flex flex-col h-full my-2">
                        <div class="flex justify-between shadow-md bg-white mx-6 my-1 p-2">
                                <p>CAST DAY</p>
                                <span>7:00 am - 7:00 pm</span>
                        </div>

                        <div class="flex justify-between shadow-md bg-white mx-6 my-1 p-2">
                                <p>HIGH SCHOOL DAY</p>
                                <span>7:00 am - 7:00 pm</span>
                        </div>

                        <div class="flex justify-between shadow-md bg-white mx-6 my-1 p-2">
                                <p>CCJ RECOLLECTION DAY</p>
                                <span>7:00 am - 7:00 pm</span>
                        </div>
                      
                        

                </div>
        
        </div>
        </div>
        <div class="w-full bg-gray-100 rounded-xl border-2 border-gray-500">
                <div class="p-4 border-b-2 border-gray-500">
                        <h1 class="text-2xl font-medium">Event Updates</h1>
                </div>
                <div class="flex flex-col space-y-2">

             
                <div class="flex items-center justify-center shadow-md p-2 mx-4 my-2">
                        <i class="fa-regular fa-bell fa-light fa-2xl"></i>
                        <div class="ml-6">
                                <p class="font-bold">Upcoming Event: CAST DAY</p>
                                <span>November 25, 2024 at 7:00 am - 7:00 pm</span>
                        </div>
                </div>
                <div class="flex items-center justify-center shadow-md p-2 mx-4 my-2">
                    <i class="fa-regular fa-bell fa-light fa-2xl"></i>
                        <div class="ml-6">
                                <p class="font-bold">Upcoming Event: CAST DAY</p>
                                <span>November 25, 2024 at 7:00 am - 7:00 pm</span>
                        </div>
                </div>
                <div class="flex items-center justify-center shadow-md p-2 mx-4 my-2">
                    <i class="fa-regular fa-bell fa-light fa-2xl"></i>
                        <div class="ml-6">
                                <p class="font-bold">Upcoming Event: CAST DAY</p>
                                <span>November 25, 2024 at 7:00 am - 7:00 pm</span>
                        </div>
                </div>

               
        </div>

        </div>
    </div>

      
</template>


<script>
export default {
    data() {
        return {
            currentMonth: new Date().toISOString().substr(0, 7), 
        };
    },
    computed: {
        daysInMonth() {
            const [year, month] = this.currentMonth.split('-').map(Number);
            const date = new Date(year, month, 1);
            const days = [];
            const firstDay = date.getDay(); 
            const lastDate = new Date(year, month + 1, 0).getDate(); 

          
            for (let i = 0; i < firstDay; i++) {
                days.push(null);
            }

            
            for (let i = 1; i <= lastDate; i++) {
                days.push(i);
            }

            return days;
        },
    },
    mounted() {
        
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); 
        this.currentMonth = `${year}-${month}`;
    },
};
</script>