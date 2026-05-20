<script setup>
import Navbar from './Navbar.vue'
// We will just use a simple placeholder for sidebar in this PoC
</script>

<template>
  <div class="page-wrapper">
    <Navbar />
    
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
.page-wrapper {
  padding-top: 70px; /* offset for fixed navbar */
}
</style>
