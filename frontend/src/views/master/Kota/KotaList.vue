<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold mb-0">Data Kota / Kabupaten</h5>
            <button @click="openForm()" class="btn btn-primary btn-sm">
              <i class="ti ti-plus"></i> Tambah Kota/Kabupaten
            </button>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
              <span class="me-2">Show</span>
              <select v-model="params.limit" @change="onLimitChange" class="form-select form-select-sm w-auto">
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
              </select>
            </div>
            <div>
              <input type="text" v-model="params.search" @input="onSearchInput" class="form-control form-control-sm" placeholder="Cari kota/kabupaten...">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table border table-striped table-bordered text-nowrap">
              <thead>
                <!-- start row -->
                <tr>
                  <th @click="toggleSort('kota_kabupaten')" style="cursor: pointer;" class="user-select-none">
                    Kota/Kabupaten
                    <i v-if="params.sort_by === 'kota_kabupaten'" :class="params.sort_dir === 'asc' ? 'ti ti-sort-ascending' : 'ti ti-sort-descending'" class="ms-1"></i>
                    <i v-else class="ti ti-arrows-sort text-muted ms-1"></i>
                  </th>
                  <th @click="toggleSort('provinsi')" style="cursor: pointer;" class="user-select-none">
                    Provinsi
                    <i v-if="params.sort_by === 'provinsi'" :class="params.sort_dir === 'asc' ? 'ti ti-sort-ascending' : 'ti ti-sort-descending'" class="ms-1"></i>
                    <i v-else class="ti ti-arrows-sort text-muted ms-1"></i>
                  </th>
                  <th>Aksi</th>
                </tr>
                <!-- end row -->
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="3" class="text-center py-4">Loading...</td>
                </tr>
                <tr v-else-if="items.length === 0">
                  <td colspan="3" class="text-center py-4">Data tidak ditemukan.</td>
                </tr>
                <tr v-else v-for="(item, index) in items" :key="item.id">
                  <td>{{ item.kota_kabupaten }}</td>
                  <td>{{ item.provinsi_rel ? item.provinsi_rel.provinsi : '-' }}</td>
                  <td>
                    <button @click="openForm(item)" class="btn btn-sm btn-primary me-2">Edit</button>
                    <button @click="confirmDelete(item)" class="btn btn-sm btn-danger">Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3" v-if="total > 0">
            <div>
              Showing {{ (params.page - 1) * params.limit + 1 }} to {{ Math.min(params.page * params.limit, total) }} of {{ total }} entries
            </div>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: params.page === 1 }">
                  <button class="page-link" @click="changePage(params.page - 1)">Previous</button>
                </li>
                <li class="page-item" v-for="p in totalPages" :key="p" :class="{ active: p === params.page }">
                  <button class="page-link" @click="changePage(p)">{{ p }}</button>
                </li>
                <li class="page-item" :class="{ disabled: params.page === totalPages }">
                  <button class="page-link" @click="changePage(params.page + 1)">Next</button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <!-- Modal Form -->
      <div class="modal fade" id="kotaModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="kotaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="kotaModalLabel">{{ isEdit ? 'Edit' : 'Tambah' }} Kota/Kabupaten</h5>
              <button type="button" class="btn-close" @click="closeForm" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitForm">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Provinsi</label>
                  <select class="select2 form-control" id="select-provinsi" style="width: 100%; height: 36px" required>
                    <option value="" disabled selected>Pilih Provinsi...</option>
                    <option v-for="prov in provinsiOptions" :key="prov.id" :value="prov.id">
                      {{ prov.provinsi }}
                    </option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Nama Kota / Kabupaten</label>
                  <input type="text" class="form-control" v-model="formData.kota_kabupaten" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" @click="closeForm">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                  {{ loadingSubmit ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

// API base config
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add token to requests
api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// State for List
const items = ref([])
const total = ref(0)
const loading = ref(false)
const provinsiOptions = ref([])
const params = ref({
  page: 1,
  limit: 10,
  search: '',
  sort_by: 'kota_kabupaten',
  sort_dir: 'asc'
})

const totalPages = computed(() => {
  return Math.ceil(total.value / params.value.limit)
})

// State for Form
const showForm = ref(false)
const isEdit = ref(false)
const loadingSubmit = ref(false)
const formData = ref({
  id: null,
  kota_kabupaten: '',
  provinsi: ''
})

let searchTimeout = null
const onSearchInput = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    params.value.page = 1
    fetchData()
  }, 500)
}

const onLimitChange = () => {
  params.value.page = 1
  fetchData()
}

const toggleSort = (column) => {
  if (params.value.sort_by === column) {
    params.value.sort_dir = params.value.sort_dir === 'asc' ? 'desc' : 'asc'
  } else {
    params.value.sort_by = column
    params.value.sort_dir = 'asc'
  }
  params.value.page = 1
  fetchData()
}

const fetchProvinsiOptions = async () => {
  try {
    const response = await api.post('/provinsi/all')
    provinsiOptions.value = response.data.data
  } catch (error) {
    console.error('Error fetching provinsi options:', error)
  }
}

const fetchData = async () => {
  try {
    loading.value = true
    const response = await api.post('/kota/list', {
      page: params.value.page,
      limit: params.value.limit,
      search: params.value.search,
      sort_by: params.value.sort_by,
      sort_dir: params.value.sort_dir
    })
    
    const resData = response.data.data
    items.value = resData.data
    total.value = resData.total
  } catch (error) {
    console.error('Error fetching data:', error)
    alert('Gagal mengambil data.')
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    params.value.page = page
    fetchData()
  }
}

let formModal = null

const openForm = (item = null) => {
  if (item) {
    isEdit.value = true
    formData.value = {
      id: item.id,
      kota_kabupaten: item.kota_kabupaten,
      provinsi: item.provinsi
    }
  } else {
    isEdit.value = false
    formData.value = {
      id: null,
      kota_kabupaten: '',
      provinsi: ''
    }
  }
  
  if (formModal) {
    formModal.show()
  }

  // Initialize and Sync state to Select2 visually after modal opens
  setTimeout(() => {
    const selectEl = window.$('#select-provinsi')
    
    if (!selectEl.hasClass("select2-hidden-accessible")) {
      selectEl.select2({
        dropdownParent: window.$('#kotaModal'),
        placeholder: 'Pilih Provinsi...'
      })
      
      selectEl.on('change', function () {
        formData.value.provinsi = window.$(this).val()
      })
    }
    
    const val = formData.value.provinsi || ''
    selectEl.val(val).trigger('change')
  }, 200)
}

const closeForm = () => {
  if (formModal) {
    formModal.hide()
  }
  setTimeout(() => {
    formData.value = { id: null, kota_kabupaten: '', provinsi: '' }
    window.$('#select-provinsi').val('').trigger('change')
  }, 300) // reset after animation
}

const submitForm = async () => {
  try {
    loadingSubmit.value = true
    if (isEdit.value) {
      await api.post('/kota/update', {
        id: formData.value.id,
        kota_kabupaten: formData.value.kota_kabupaten,
        provinsi: formData.value.provinsi
      })
    } else {
      await api.post('/kota/create', {
        kota_kabupaten: formData.value.kota_kabupaten,
        provinsi: formData.value.provinsi
      })
    }
    closeForm()
    fetchData() // Refresh
    
    // Show SweetAlert success
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: isEdit.value ? 'Data berhasil diubah!' : 'Data berhasil ditambahkan!',
      timer: 1500,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Error submitting data:', error)
    alert('Gagal menyimpan data.')
  } finally {
    loadingSubmit.value = false
  }
}

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: 'Konfirmasi Hapus',
    html: `Apakah Anda yakin ingin menghapus kota/kabupaten <strong>${item.kota_kabupaten}</strong>?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#fa896b', // danger color in template
    cancelButtonColor: '#5a6a85', // secondary color
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    reverseButtons: true
  })

  if (result.isConfirmed) {
    executeDelete(item.id)
  }
}

const executeDelete = async (id) => {
  try {
    await api.post('/kota/delete', { id })
    fetchData() // Refresh list
    
    // Show SweetAlert success
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Data berhasil dihapus!',
      timer: 1500,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Error deleting data:', error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Terjadi kesalahan saat menghapus data.',
      confirmButtonColor: '#5d87ff'
    })
  }
}

onMounted(() => {
  if (window.bootstrap) {
    formModal = new window.bootstrap.Modal(document.getElementById('kotaModal'))
  }
  fetchProvinsiOptions()
  fetchData()
})
</script>
