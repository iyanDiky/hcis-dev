<template>
  <header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
      </ul>
      <div class="d-block d-lg-none">
        <img src="/assets/images/logos/dark-logo.svg" class="dark-logo" width="180" alt="" />
      </div>
      <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="p-2">
          <i class="ti ti-dots fs-7"></i>
        </span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="d-flex align-items-center justify-content-between">
          <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
            <i class="ti ti-align-justified fs-7"></i>
          </a>
          <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
            <li class="nav-item dropdown">
              <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex align-items-center">
                  <div class="user-profile-img">
                    <img src="/assets/images/profile/user-1.jpg" class="rounded-circle" width="35" height="35" alt="" />
                  </div>
                </div>
              </a>
              <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="profile-dropdown position-relative" data-simplebar>
                  <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                    <img src="/assets/images/profile/user-1.jpg" class="rounded-circle" width="80" height="80" alt="" />
                    <div class="ms-3">
                      <h5 class="mb-1 fs-3">{{ user?.username || 'Admin' }}</h5>
                      <p class="mb-0 d-flex text-dark align-items-center gap-2">
                        <i class="ti ti-mail fs-4"></i> info@hcis.com
                      </p>
                    </div>
                  </div>
                  <div class="d-grid py-4 px-7 pt-8">
                    <button @click="logout" class="btn btn-outline-primary">Log Out</button>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }
});

const logout = () => {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user');
  router.push('/login');
};
</script>
