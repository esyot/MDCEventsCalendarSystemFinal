<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

const form = useForm({
  user: '',
  password: ''
});

const submit = () => {
  form.post('/login');
};

defineOptions({ layout: GuestLayout });
</script>

<template>
  <div class="flex">

      <div class="mt-6 flex bg-white shadow-md p-4 w-1/2">
      <img src="/resources/css/mdc.png" alt="" class="rounded-full w-[200px] h-[200px] justify-center mt-5 ">
      </div>

      <div class="mt-6 flex flex-col bg-blue-900 shadow-md p-4">
          <h1 class="p2 flex justify-center text-lg font-bold text-white">Log In</h1>
            <form @submit.prevent="submit" class="p-2">
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
                  class="block p-2 border border-gray-300 w-full rounded mb-4" 
                  name="password" 
                  v-model="form.password"/>
            
                <span v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</span>
              </div>
              <span v-if="form.errors.user" class="text-red-500">{{ form.errors.user }}</span>

              <div class=" flex justify-end">
                <button class="px-4 py-2 bg-blue-500 text-white hover:text-blue-900 hover:bg-blue-400 rounded" type="submit">Login</button>
              </div>

            </form>     
      </div>
  </div>
 
</template>