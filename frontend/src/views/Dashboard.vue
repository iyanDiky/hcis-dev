<template>
  <div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Dashboard</h5>
            <p class="mb-0">Welcome to your dashboard, {{ user?.username }}!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const user = ref(null);

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }
});
</script>
