<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title">Data Pribadi SDM</h4>
          <button class="btn btn-primary" @click="showAddModal">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <!-- Search & Limit -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <span class="me-2">Tampilkan</span>
                  <select v-model="params.limit" class="form-select w-auto" @change="fetchData">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                  </select>
                  <span class="ms-2">data</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group">
                  <input 
                    type="text" 
                    class="form-control" 
                    placeholder="Cari NIK, Nama, Email, No. Telp..." 
                    v-model="searchInput"
                    @keyup.enter="handleSearch"
                  >
                  <button class="btn btn-outline-secondary" type="button" @click="handleSearch">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Table -->
            <table class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center" width="5%">No</th>
                  <th @click="handleSort('nik')" class="cursor-pointer">
                    NIK
                    <i v-if="params.sort_by === 'nik'" :class="['fas', params.sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                    <i v-else class="fas fa-sort text-muted"></i>
                  </th>
                  <th @click="handleSort('nama')" class="cursor-pointer">
                    Nama
                    <i v-if="params.sort_by === 'nama'" :class="['fas', params.sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                    <i v-else class="fas fa-sort text-muted"></i>
                  </th>
                  <th @click="handleSort('email')" class="cursor-pointer">
                    Email
                    <i v-if="params.sort_by === 'email'" :class="['fas', params.sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                    <i v-else class="fas fa-sort text-muted"></i>
                  </th>
                  <th @click="handleSort('nomor_telp')" class="cursor-pointer">
                    No. Telp
                    <i v-if="params.sort_by === 'nomor_telp'" :class="['fas', params.sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                    <i v-else class="fas fa-sort text-muted"></i>
                  </th>
                  <th>L/P</th>
                  <th class="text-center" width="15%">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center">Loading...</td>
                </tr>
                <tr v-else-if="items.length === 0">
                  <td colspan="7" class="text-center">Data tidak ditemukan</td>
                </tr>
                <tr v-else v-for="(item, index) in items" :key="item.id">
                  <td class="text-center">{{ (params.page - 1) * params.limit + index + 1 }}</td>
                  <td>{{ item.nik }}</td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.email }}</td>
                  <td>{{ item.nomor_telp }}</td>
                  <td>{{ item.jk }}</td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-info me-1" @click="showEditModal(item)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" @click="handleDelete(item.id)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div>
                Menampilkan {{ startIndex }} sampai {{ endIndex }} dari {{ total }} data
              </div>
              <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: params.page === 1 }">
                  <button class="page-link" @click="changePage(params.page - 1)">Previous</button>
                </li>
                <li class="page-item disabled">
                  <span class="page-link">{{ params.page }}</span>
                </li>
                <li class="page-item" :class="{ disabled: endIndex >= total }">
                  <button class="page-link" @click="changePage(params.page + 1)">Next</button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl"> <!-- Using Extra Large modal for more fields -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Edit Data Pribadi' : 'Tambah Data Pribadi' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleSubmit">
              <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                  <h6 class="text-primary mb-3">Informasi Utama</h6>
                  
                  <div class="mb-3">
                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.nik" required maxlength="16">
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.nama" required>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" v-model="form.email" required>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.nomor_telp" required maxlength="15">
                  </div>

                  <div class="row">
                    <div class="col-6 mb-3">
                      <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                      <select class="form-select" v-model="form.jk" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                      </select>
                    </div>
                    <div class="col-6 mb-3">
                      <label class="form-label">Golongan Darah</label>
                      <select class="form-select" v-model="form.gol_darah">
                        <option value="">-- Pilih --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-6 mb-3">
                      <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" v-model="form.tempat_lahir" required>
                    </div>
                    <div class="col-6 mb-3">
                      <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" v-model="form.tanggal_lahir" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-6 mb-3">
                      <label class="form-label">Agama <span class="text-danger">*</span></label>
                      <select class="form-select" v-model="form.agama" required>
                        <option value="">-- Pilih --</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                      </select>
                    </div>
                    <div class="col-6 mb-3">
                      <label class="form-label">Status Pernikahan <span class="text-danger">*</span></label>
                      <select class="form-select" v-model="form.status_pernikahan" required>
                        <option value="">-- Pilih --</option>
                        <option value="B">Belum Menikah (B)</option>
                        <option value="M">Menikah (M)</option>
                        <option value="P">Pernah Menikah (P)</option>
                        <option value="J">Janda (J)</option>
                        <option value="D">Duda (D)</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                  <h6 class="text-primary mb-3">Informasi Lainnya & Alamat</h6>

                  <div class="mb-3">
                    <label class="form-label">NPWP</label>
                    <input type="text" class="form-control" v-model="form.npwp">
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Kota/Kabupaten KTP <span class="text-danger">*</span></label>
                    <select class="form-select" v-model="form.kota_kab_ktp" required>
                      <option value="">-- Pilih Kota/Kabupaten --</option>
                      <option v-for="kota in kotaOptions" :key="kota.id" :value="kota.id">
                        {{ kota.kota_kabupaten }} ({{ kota.provinsi_rel ? kota.provinsi_rel.provinsi : '-' }})
                      </option>
                    </select>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Alamat Sesuai KTP <span class="text-danger">*</span></label>
                    <textarea class="form-control" v-model="form.alamat_ktp" rows="2" required></textarea>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Kota/Kabupaten Domisili <span class="text-danger">*</span></label>
                    <select class="form-select" v-model="form.kota_kab_domisili" required>
                      <option value="">-- Pilih Kota/Kabupaten --</option>
                      <option v-for="kota in kotaOptions" :key="kota.id" :value="kota.id">
                        {{ kota.kota_kabupaten }} ({{ kota.provinsi_rel ? kota.provinsi_rel.provinsi : '-' }})
                      </option>
                    </select>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Alamat Domisili <span class="text-danger">*</span></label>
                    <textarea class="form-control" v-model="form.alamat_domisili" rows="2" required></textarea>
                  </div>

                  <h6 class="text-primary mt-4 mb-3">Lampiran / Berkas</h6>
                  
                  <div class="mb-3">
                    <label class="form-label">Foto (Opsional)</label>
                    <input type="file" class="form-control" accept="image/*" @change="e => form.foto = e.target.files[0]">
                    <div v-if="isEdit && typeof form.foto === 'string' && form.foto" class="mt-2">
                      <img :src="'http://localhost:8000' + form.foto" alt="Foto" width="100" class="img-thumbnail">
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-6 mb-3">
                      <label class="form-label">Spesimen TTD (Opsional)</label>
                      <input type="file" class="form-control" accept="image/*" @change="e => form.spesimen_tanda_tangan = e.target.files[0]">
                      <div v-if="isEdit && typeof form.spesimen_tanda_tangan === 'string' && form.spesimen_tanda_tangan" class="mt-2">
                        <img :src="'http://localhost:8000' + form.spesimen_tanda_tangan" alt="TTD" width="100" class="img-thumbnail">
                      </div>
                    </div>
                    <div class="col-6 mb-3">
                      <label class="form-label">Spesimen Paraf (Opsional)</label>
                      <input type="file" class="form-control" accept="image/*" @change="e => form.spesimen_paraf = e.target.files[0]">
                      <div v-if="isEdit && typeof form.spesimen_paraf === 'string' && form.spesimen_paraf" class="mt-2">
                        <img :src="'http://localhost:8000' + form.spesimen_paraf" alt="Paraf" width="100" class="img-thumbnail">
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="modal-footer px-0 pb-0 mt-3 border-top pt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="submitting">Tutup</button>
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  {{ isEdit ? 'Update' : 'Simpan' }}
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// State
const items = ref([])
const total = ref(0)
const loading = ref(false)
const submitting = ref(false)
const searchInput = ref('')
const formModal = ref(null)
const isEdit = ref(false)
const kotaOptions = ref([])

// Params for API
const params = ref({
  page: 1,
  limit: 10,
  search: '',
  sort_by: 'nama',
  sort_dir: 'asc'
})

// Default form structure
const defaultForm = {
  id: '',
  email: '',
  nik: '',
  nama: '',
  jk: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  agama: '',
  gol_darah: '',
  status_pernikahan: '',
  foto: '',
  spesimen_tanda_tangan: '',
  spesimen_paraf: '',
  npwp: '',
  nomor_telp: '',
  alamat_ktp: '',
  kota_kab_ktp: '',
  alamat_domisili: '',
  kota_kab_domisili: ''
}
const form = ref({ ...defaultForm })

// Computed properties for pagination info
const startIndex = computed(() => {
  if (total.value === 0) return 0
  return (params.value.page - 1) * params.value.limit + 1
})

const endIndex = computed(() => {
  const end = params.value.page * params.value.limit
  return end > total.value ? total.value : end
})

// Methods
const fetchKota = async () => {
  try {
    const response = await api.post('/kota/all')
    kotaOptions.value = response.data.data
  } catch (error) {
    console.error('Error fetching kota:', error)
  }
}

const fetchData = async () => {
  try {
    loading.value = true
    const response = await api.post('/sdm-data/list', {
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
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Terjadi kesalahan saat mengambil data.'
    })
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  params.value.search = searchInput.value
  params.value.page = 1
  fetchData()
}

const handleSort = (column) => {
  if (params.value.sort_by === column) {
    params.value.sort_dir = params.value.sort_dir === 'asc' ? 'desc' : 'asc'
  } else {
    params.value.sort_by = column
    params.value.sort_dir = 'asc'
  }
  fetchData()
}

const changePage = (newPage) => {
  params.value.page = newPage
  fetchData()
}

const showAddModal = () => {
  isEdit.value = false
  form.value = { ...defaultForm }
  formModal.value.show()
}

const showEditModal = (item) => {
  isEdit.value = true
  form.value = { ...item }
  formModal.value.show()
}

const handleSubmit = async () => {
  try {
    submitting.value = true
    const endpoint = isEdit.value ? '/sdm-data/update' : '/sdm-data/create'
    
    // Create FormData for file uploads
    const formData = new FormData()
    for (const key in form.value) {
      if (form.value[key] !== null && form.value[key] !== undefined) {
        // If it's a file field and it's a string, it means it hasn't been changed during edit
        if (['foto', 'spesimen_tanda_tangan', 'spesimen_paraf'].includes(key) && typeof form.value[key] === 'string') {
          continue; // Don't send string URLs back to the server for files
        }
        formData.append(key, form.value[key])
      }
    }

    const response = await api.post(endpoint, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    if (response.data.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: response.data.message
      })
      formModal.value.hide()
      fetchData()
    }
  } catch (error) {
    console.error('Submit error:', error)
    const errorMsg = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.'
    // Grab validation errors if any
    const errors = error.response?.data?.errors
    let detailedMsg = errorMsg
    if (errors) {
      detailedMsg += '<br><br>' + Object.values(errors).flat().join('<br>')
    }
    
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      html: detailedMsg
    })
  } finally {
    submitting.value = false
  }
}

const handleDelete = async (id) => {
  const result = await Swal.fire({
    title: 'Apakah Anda yakin?',
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  })

  if (result.isConfirmed) {
    try {
      const response = await api.post('/sdm-data/delete', { id })
      
      if (response.data.status === 'success') {
        Swal.fire(
          'Terhapus!',
          'Data berhasil dihapus.',
          'success'
        )
        // Adjust page if we deleted the last item on current page
        if (items.value.length === 1 && params.value.page > 1) {
          params.value.page--
        }
        fetchData()
      }
    } catch (error) {
      console.error('Delete error:', error)
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: error.response?.data?.message || 'Terjadi kesalahan saat menghapus data.'
      })
    }
  }
}

// Lifecycle
onMounted(() => {
  // Initialize Bootstrap Modal
  const modalEl = document.getElementById('formModal')
  formModal.value = new window.bootstrap.Modal(modalEl)
  
  // Initial fetch
  fetchKota()
  fetchData()
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
.cursor-pointer:hover {
  background-color: #f8f9fa;
}
</style>
