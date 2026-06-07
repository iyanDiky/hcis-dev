<template>
  <div class="row">
    <div class="col-xl-7 col-xxl-8">
      <a href="/" class="text-nowrap logo-img d-block px-4 py-9 w-100">
        <img src="/assets/images/logos/dark-logo.svg" width="180" alt="logo">
      </a>
      <div class="d-none d-xl-flex align-items-center justify-content-center" style="height: calc(100vh - 80px);">
        <img src="/assets/images/backgrounds/login-security.svg" alt="" class="img-fluid" width="500">
      </div>
    </div>
    <div class="col-xl-5 col-xxl-4">
      <div class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4">
        <div class="col-sm-8 col-md-6 col-xl-9">
          <h2 class="mb-3 fs-7 fw-bolder">Welcome to HCIS</h2>
          <p class=" mb-9">Your Admin Dashboard</p>
          
          <form @submit.prevent="handleLogin">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" v-model="username" class="form-control" id="username">
            </div>
            <div class="mb-4">
              <label for="password" class="form-label">Password</label>
              <input type="password" v-model="password" class="form-control" id="password">
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="form-check">
                <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                <label class="form-check-label text-dark" for="flexCheckChecked">
                  Remember this Device
                </label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2" :disabled="loading">
              {{ loading ? 'Signing in...' : 'Sign In' }}
            </button>
            <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('admin');
const password = ref('password');
const errorMessage = ref('');
const loading = ref(false);
const router = useRouter();

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value
      })
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      errorMessage.value = data.message || 'Login failed';
      return;
    }
    
    localStorage.setItem('auth_token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    router.push('/');
  } catch (error) {
    errorMessage.value = 'Failed to connect to server';
  } finally {
    loading.value = false;
  }
};
</script>
