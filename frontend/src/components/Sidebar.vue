<template>
  <aside class="left-sidebar">
    <div>
      <div class="brand-logo d-flex align-items-center justify-content-between">
        <router-link to="/" class="text-nowrap logo-img">
          <img src="/assets/images/logos/dark-logo.svg" class="dark-logo" width="180" alt="" />
        </router-link>
        <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
          <i class="ti ti-x fs-8 text-muted"></i>
        </div>
      </div>
      <nav class="sidebar-nav scroll-sidebar" data-simplebar>
        <ul id="sidebarnav">
          <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Home</span>
          </li>
          <li class="sidebar-item">
            <router-link class="sidebar-link" to="/" aria-expanded="false">
              <span>
                <i class="ti ti-aperture"></i>
              </span>
              <span class="hide-menu">Dashboard</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script setup>
</script>
