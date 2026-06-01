<template>
  <div class="topbar">
      <!-- Navbar -->
      <nav class="navbar-custom" id="navbar-custom">    
          <ul class="list-unstyled topbar-nav float-end mb-0">  
              <li class="dropdown">
                  <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-bs-toggle="dropdown" href="#" role="button"
                      aria-haspopup="false" aria-expanded="false">
                      <span class="ms-1 nav-user-name hidden-sm">{{ userName }}</span>
                      <img src="/assets/images/users/user-5.jpg" alt="profile-user" class="rounded-circle thumb-xs" />                                 
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="#"><i class="ti ti-user font-16 me-1 align-text-bottom"></i> Profile</a>
                      <a class="dropdown-item" href="#"><i class="ti ti-settings font-16 me-1 align-text-bottom"></i> Settings</a>
                      <div class="dropdown-divider mb-0"></div>
                      <a class="dropdown-item" href="javascript:void(0);" @click="logout"><i class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout</a>
                  </div>
              </li>
          </ul><!--end topbar-nav-->

          <ul class="list-unstyled topbar-nav mb-0">                        
              <li>
                  <button class="nav-link button-menu-mobile waves-effect waves-light">
                      <i class="ti ti-menu-2 nav-icon"></i>
                  </button>
              </li>
          </ul>
      </nav>
      <!-- end navbar-->
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const userName = ref('Admin HCIS')

onMounted(() => {
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      if (parsed.sdm_relation && parsed.sdm_relation.data && parsed.sdm_relation.data.nama) {
        userName.value = parsed.sdm_relation.data.nama
      }
    } catch (e) {
      console.error(e)
    }
  }
})

const logout = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (token) {
      await axios.post('http://localhost:8000/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
    }
  } catch (error) {
    console.error('Logout error', error)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}
</script>
