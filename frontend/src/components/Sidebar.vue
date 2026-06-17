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
          <template v-for="(item, index) in menuItems" :key="index">
            <!-- Nav Small Cap (Header) -->
            <li v-if="item.type === 'cap'" class="nav-small-cap">
              <i :class="item.icon || 'ti ti-dots'" class="nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">{{ item.title }}</span>
            </li>

            <!-- Link (Single Menu) -->
            <li v-else-if="item.type === 'link'" class="sidebar-item">
              <router-link class="sidebar-link" :to="item.path" exact-active-class="active" aria-expanded="false">
                <span>
                  <i :class="item.icon"></i>
                </span>
                <span class="hide-menu">{{ item.title }}</span>
              </router-link>
            </li>

            <!-- Dropdown Menu -->
            <li v-else-if="item.type === 'dropdown'" class="sidebar-item" :class="{ 'selected': item.isOpen }">
              <a class="sidebar-link has-arrow" :class="{ 'active': item.isOpen }" href="javascript:void(0)" @click.prevent="toggleMenu(index)" :aria-expanded="item.isOpen">
                <span class="d-flex">
                  <i :class="item.icon"></i>
                </span>
                <span class="hide-menu">{{ item.title }}</span>
              </a>
              <ul :aria-expanded="item.isOpen" class="collapse first-level" :class="{ 'in': item.isOpen }">
                <li class="sidebar-item" v-for="(child, childIndex) in item.children" :key="childIndex">
                  <router-link :to="child.path" class="sidebar-link" active-class="active">
                    <div class="round-16 d-flex align-items-center justify-content-center">
                      <i :class="child.icon || 'ti ti-circle'"></i>
                    </div>
                    <span class="hide-menu">{{ child.title }}</span>
                  </router-link>
                </li>
              </ul>
            </li>
          </template>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

// Array menuItems untuk me-render sidebar menu secara dinamis.
// Nantinya array ini bisa di-filter berdasarkan akses pengguna (Role/Permission).
const menuItems = ref([
  {
    type: 'cap',
    title: 'Home',
    icon: 'ti ti-dots'
  },
  {
    type: 'link',
    title: 'Dashboard',
    icon: 'ti ti-aperture',
    path: '/'
  },
  {
    type: 'cap',
    title: 'Data',
    icon: 'ti ti-dots'
  },
  {
    type: 'dropdown',
    title: 'Master',
    icon: 'ti ti-database',
    isOpen: false,
    children: [
      {
        title: 'Provinsi',
        path: '/provinsi',
        icon: 'ti ti-circle'
      },
      {
        title: 'Kota/Kabupaten',
        path: '/kota',
        icon: 'ti ti-circle'
      }
    ]
  },
  {
    type: 'dropdown',
    title: 'SDM',
    icon: 'ti ti-users',
    isOpen: false,
    children: [
      {
        title: 'Jenis SDM',
        path: '/sdm-jenis',
        icon: 'ti ti-circle'
      }
    ]
  }
]);

const toggleMenu = (index) => {
  if (menuItems.value[index].type === 'dropdown') {
    menuItems.value[index].isOpen = !menuItems.value[index].isOpen;
  }
};

const setActiveMenu = () => {
  menuItems.value.forEach(item => {
    if (item.type === 'dropdown') {
      // Check if any child matches current path exactly or as a prefix
      const isActive = item.children.some(child => route.path === child.path || route.path.startsWith(child.path + '/'));
      item.isOpen = isActive;
    }
  });
};

onMounted(() => {
  setActiveMenu();
});

watch(() => route.path, () => {
  setActiveMenu();
});
</script>
