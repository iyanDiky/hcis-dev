<template>
  <div class="page-wrapper" id="main-wrapper" data-theme="blue_theme" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <Sidebar />
    <div class="body-wrapper">
      <Header />
      <div class="container-fluid">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import Sidebar from '../components/Sidebar.vue';
import Header from '../components/Header.vue';
</script>
