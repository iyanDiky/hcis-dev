<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const username = ref('')
const password = ref('')
const errorMessage = ref('')

onMounted(() => {
  document.body.classList.add('auth-page')
  document.body.style.backgroundImage = "url('/assets/images/p-1.png')"
  document.body.style.backgroundSize = "cover"
  document.body.style.backgroundPosition = "center center"
})

onUnmounted(() => {
  document.body.classList.remove('auth-page')
  document.body.style.backgroundImage = ""
})

const handleLogin = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      username: username.value,
      password: password.value
    })
    
    // Store token (dummy or real)
    localStorage.setItem('auth_token', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    
    // Redirect to dashboard
    router.push('/')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login failed. Please check your credentials.'
  }
}
</script>

<template>
  <div>
    <div class="container-md">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="#" class="logo logo-admin">
                                            <img src="/assets/images/logo-sm.png" height="50" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white font-18">Let's Get Started Metrica</h4>   
                                        <p class="text-muted  mb-0">Sign in to continue to Metrica.</p>  
                                    </div>
                                </div>
                                <div class="card-body pt-0">                                    
                                    <form class="my-4" @submit.prevent="handleLogin">            
                                        <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                                        
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="username">Email / Username</label>
                                            <input type="text" class="form-control" id="username" v-model="username" placeholder="Enter username (admin@admin.com)">                               
                                        </div>
            
                                        <div class="form-group">
                                            <label class="form-label" for="userpassword">Password</label>                                            
                                            <input type="password" class="form-control" id="userpassword" v-model="password" placeholder="Enter password (password)">                            
                                        </div>
            
                                        <div class="form-group row mt-3">
                                            <div class="col-sm-6">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input" type="checkbox" id="customSwitchSuccess">
                                                    <label class="form-check-label" for="customSwitchSuccess">Remember me</label>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 text-end">
                                                <a href="#" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot password?</a>                                    
                                            </div>
                                        </div>
            
                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="submit">Log In <i class="fas fa-sign-in-alt ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>                           
                                    </form>
                                    <div class="m-3 text-center text-muted">
                                        <p class="mb-0">Don't have an account ?  <a href="#" class="text-primary ms-2">Free Resister</a></p>
                                    </div>
                                    <hr class="hr-dashed mt-4">
                                    <div class="text-center mt-n5">
                                        <h6 class="card-bg px-3 my-4 d-inline-block">Or Login With</h6>
                                    </div>
                                    <div class="d-flex justify-content-center mb-1">
                                        <a href="#" class="d-flex justify-content-center align-items-center thumb-sm bg-soft-primary rounded-circle me-2">
                                            <i class="fab fa-facebook align-self-center"></i>
                                        </a>
                                        <a href="#" class="d-flex justify-content-center align-items-center thumb-sm bg-soft-info rounded-circle me-2">
                                            <i class="fab fa-twitter align-self-center"></i>
                                        </a>
                                        <a href="#" class="d-flex justify-content-center align-items-center thumb-sm bg-soft-danger rounded-circle">
                                            <i class="fab fa-google align-self-center"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>
