<script setup>
import { onMounted } from 'vue'
import axios from 'axios'
import Navbar from './Navbar.vue'
import Sidebar from './Sidebar.vue'

onMounted(async () => {
  try {
    // This will trigger the global 401 interceptor if the token was revoked (e.g., logged in from another device)
    await axios.get('/api/user')
  } catch (e) {
    // Error is handled globally
  }
})
</script>

<template>
  <Sidebar />
  <Navbar />
  
  <div class="page-wrapper">
    <div class="page-content-tab">
      <div class="container-fluid">
        <router-view></router-view>
      </div>
      
      <!-- Footer -->
      <footer class="footer text-center text-sm-start">
        &copy; 2026 HCIS <span class="text-muted d-none d-sm-inline-block float-end">Crafted with <i class="mdi mdi-heart text-danger"></i> by Admin</span>
      </footer>
    </div>
  </div>
</template>

<style scoped>
/* Optional scoped styling for layout */
</style>
