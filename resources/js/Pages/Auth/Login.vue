<script setup>
import { Inertia } from '@inertiajs/inertia';
import { ref, reactive } from 'vue';
import GuesLayout from '../../Layouts/GuestLayout.vue';

defineOptions({ layout: GuesLayout});

// Creating reactive form state
const form = reactive({
  user: '',
  password: ''
});

// Ref for storing error messages
const errors = ref({});

// Submit function that uses Inertia for handling form submission
const submit = () => {
  Inertia.post(route('login.sud'), form, {
    onError: (errorBag) => {
      errors.value = errorBag;
    }
  });
};
</script>

<template>
  <div>
    <h1 class="text-4xl">Log In</h1>

    <form @submit.prevent="submit">
      <div class="mb-2">
        <label for="username">Username</label>
        <input 
          type="text" 
          class="border-2 border-black" 
          name="user" 
          v-model="form.user"
          :class="{ 'border-red-500': errors.user }"
        />
        <span v-if="errors.user" class="text-red-500">{{ errors.user }}</span>
      </div>

      <div>
        <label for="password">Password</label>
        <input 
          type="password" 
          class="border-2 border-black" 
          name="password" 
          v-model="form.password"
          :class="{ 'border-red-500': errors.password }"
        />
        <span v-if="errors.password" class="text-red-500">{{ errors.password }}</span>
      </div>

      <button class="bg-blue-600 text-white py-2 px-2" type="submit">Login</button>
    </form>
  </div>
</template>


