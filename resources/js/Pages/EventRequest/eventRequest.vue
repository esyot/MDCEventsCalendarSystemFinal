<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineOptions({ layout: MainLayout });
</script>

<template>
   
      <div class="p-2 w-full">
        <table class="w-full border">
          <thead>
            <tr class="bg-gray-300">
              <th class="px-4 py-2 text-left">Event Name</th>
              <th class="px-4 py-2 text-left">Details</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left w-8">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(event, index) in events" :key="index" class="bg-white border-b">
              <td class="px-4 py-2">{{ event.name }}</td>
              <td class="px-4 py-2">{{ event.name }}</td>
              <td class="px-4 py-2 space-x-2">
                
                <i :class = "event.isApprovedByVenueCoordinator != null ? 'fas fa-check text-green-500' : 'fas fa-x text-red-500'" ></i>
                <i :class = "event.isApprovedByAdmin != null ? 'fas fa-check text-green-500' : 'fas fa-x text-red-500'" ></i>
              </td>
              <td class="px-4 py-2 flex space-x-2">
              

                <button id="eventCoordinator" v-if="user_role == 'venue_coordinator' || user_role == 'admin' || user_role =='event_coordinator'" @click="eventView(event.id)" class="text-blue-500 hover:text-blue-700">
                  <i class="fas fa-eye"></i>
                </button>

                <button @click="editEvent(event.id)" class="text-yellow-500 hover:text-yellow-700">
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  @click="deleteEvent(event.id)" 
                  class="text-red-500 hover:text-red-700">
                  <i class="fas fa-trash-alt"></i>
                </button>

              
              </td>


      <div :id="'event-preview-' + event.id" class="flex fixed inset-0 justify-center items-center bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="bg-white w-[600px] rounded shadow-md">
          <div class="flex justify-between p-2">
            <h1 class="text-2xl font-bold">Event Details</h1>

            <button @click="eventView(event.id)" class="text-xl font-bold hover:opacity-50">&times;</button>
          </div>        

          <div class="flex justify-between p-2">
            <h1 class="font-medium">Event Name:</h1>
            <span>{{event.name}}</span>
          </div>

          <div class="flex justify-between p-2">
            <h1 class="font-medium">Event Venue:</h1>
            <span>{{event.venue_name}} at {{event.venue_building}}</span>
          </div>

          <div class="flex justify-between p-2">
            <h1  class="font-medium">Date:</h1>
            <span>{{event.date}}</span>
          </div>

          <div class="flex justify-between p-2">
            <h1  class="font-medium">Time:</h1>
            <div>
              <span>{{ formatTime(event.time_start) }}</span> to <span>{{ formatTime(event.time_end)}}</span>
            </div>
          
          </div>

          <div class="flex justify-between p-2">
            <h1  class="font-medium" >Activity Design:</h1>
            <a :href="'/admin/download-activity-design/' + event.activity_design_file_name"
    class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded">
    {{ event.activity_design_file_name }}
          </a>
          </div>

          <div v-if="user_role == 'event_coordinator'" class="flex justify-between p-2">
            <h1 class="font-medium">Comment:</h1>
            <span>{{ event.comment  }}</span>
          </div>

          <form :action="'/admin/event/comment-add/'+ event.id" method="GET" v-if="user_role == 'admin'" class=" p-2">
            <h1  class="font-medium">Comment:</h1>
            <div class="flex items-start space-x-2">
            <textarea name="comment" :value="event.comment" placeholder="Input comment here..." class="border h-20 shadow-inner w-full border-gray-300 rounded"></textarea>
           <button class="px-4 py-2 bg-blue-500 text-blue-100 hover:opacity-50 rounded">
            <i class="fas fa-paper-plane"></i>
           </button>
          </div>
</form>

          <div v-if="user_role == 'event_coordinator' && event.isApprovedByVenueCoordinator != null && event.isApprovedByAdmin != null" class="flex p-2 justify-end">
            <button @click="printDiv(event)" class="px-4 py-2 bg-green-500 text-green-100 hover:opacity-50 rounded">Print Stub</button>
          </div>

          <div id="approve-admin" v-if="user_role == 'admin' && event.isApprovedByVenueCoordinator != null && event.isApprovedByAdmin == null" class="flex justify-end space-x-1 p-2">
    <button type="button" class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded">Decline</button>
    <a :href="'/admin/event/approve/admin/'+event.id" type="button" class="px-4 py-2 bg-green-500 text-green-100 rounded">Approve</a>
  </div>

  <div id="approve-admin" v-if="user_role == 'admin' && event.isApprovedByVenueCoordinator != null && event.isApprovedByAdmin != null" class="flex justify-end space-x-1 p-2">
    <a :href="'/admin/event/retract/'+event.id" type="button" class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded">Retract</a>
  </div>

  
  <div id="approve-venue-coordinator" v-if="user_role == 'venue_coordinator' && event.isApprovedByVenueCoordinator == null" class="flex justify-end space-x-1 p-2">
    <button class="px-4 py-2 border border-gray-300 hover:opacity-50 rounded">Close</button>
    <a :href="'/admin/event/approve/venue_coordinator/'+event.id" class="px-4 py-2 bg-green-500 text-green-100 rounded">Approve</a>
  </div>
  
         
        </div>


      </div>
            </tr>
          </tbody>
        </table>
      </div>


     
  
  </template>
  
  <script>


const eventView = (id) => {

  document.getElementById('event-preview-'+id).classList.toggle('hidden');

};
export default {
  props: {

    events: {
      type: Array,
    },
    user_role: {
      type: String,
    }

  },
  methods: {
    formatTime(time) {
      const [hours, minutes] = time.split(":");
      const formattedHours = hours % 12 || 12;
      const ampm = hours < 12 ? "am" : "pm";
      return `${formattedHours}:${minutes} ${ampm}`;
    },
    printDiv(event) {
  const printWindow = window.open('', '', 'height=500, width=800');
  
  // Write HTML and style in one go
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
      <div><h2>Event: ${event.name}</h2></div>
      <div><h2>Venue: ${event.venue_name} at ${event.venue_building}</h2></div>
      ${event.date ? `<div><h2>Date: ${event.date}</h2></div>` : ''}
      ${event.time_start && event.time_end ? `<div><h2>Time: ${this.formatTime(event.time_start)} to ${this.formatTime(event.time_end)}</h2></div>` : ''}
      <div class="footer">
        <h2>JOSEPHINE D. APLACADOR, Ma.Ed.</h2>
        <i>OSAD Coordinator</i>
      </div>
    </body></html>
  `);

  printWindow.document.close();
  printWindow.print();
}
  }
}
</script>
  <style scoped>
  .container {
    max-width: 900px;
  }
  </style>
  