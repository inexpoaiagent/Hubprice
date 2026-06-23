<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Agency &amp; Dealer <span class="gradient-text">Management</span></h1>
        <p class="text-sm" style="color:#6e7f96">Manage agencies, dealers and their listings</p>
      </div>
      <span class="px-3 py-1 rounded-full text-sm" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">{{ total }} partners</span>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6 p-4 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <select v-model="filters.role" @change="fetch(1)" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
        <option value="">All Partners</option>
        <option value="agency">Agencies</option>
        <option value="dealer">Dealers</option>
      </select>
      <input v-model="filters.search" @input="debounce" placeholder="Search name or email..." class="px-4 py-2 rounded-xl text-sm flex-1 min-w-48" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="h-24 rounded-2xl animate-pulse" style="background:#0d1117"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="items.length===0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <div class="text-5xl mb-3">🏢</div>
      <p class="font-semibold" style="color:#f0f6fc">No agencies or dealers found</p>
      <p class="text-sm mt-1" style="color:#6e7f96">Users with agency/dealer role will appear here</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <div v-for="u in items" :key="u.id" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-lg shrink-0" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">
              {{ u.name?.charAt(0)?.toUpperCase() || 'A' }}
            </div>
            <div>
              <div class="flex items-center gap-2 mb-0.5">
                <p class="font-semibold text-sm" style="color:#f0f6fc">{{ u.name }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full capitalize font-medium" :style="u.role==='agency'?'background:rgba(167,139,250,0.1);color:#a78bfa':'background:rgba(86,216,255,0.1);color:#56d8ff'">{{ u.role }}</span>
                <span v-if="u.kyc_verified" class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(111,255,210,0.1);color:#6fffd2">✓ Verified</span>
                <span v-if="!u.is_active" class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(239,68,68,0.1);color:#ef4444">Banned</span>
              </div>
              <p class="text-xs" style="color:#6e7f96">{{ u.email }} · Joined {{ new Date(u.created_at).toLocaleDateString('en-GB') }}</p>
              <p class="text-xs mt-0.5" style="color:#6e7f96">
                Listings: <span style="color:#6fffd2">{{ u.listings_count||0 }}</span>
                <span v-if="u.active_subscription"> · Plan: <span style="color:#56d8ff">{{ u.active_subscription?.plan?.name }}</span></span>
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2 flex-wrap">
            <button v-if="!u.kyc_verified" @click="verify(u.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">Verify</button>
            <button @click="openEdit(u)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(86,216,255,0.08);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">Edit</button>
            <button @click="toggleActive(u)" class="px-3 py-1.5 text-xs rounded-lg" :style="u.is_active?'background:rgba(239,68,68,0.06);color:#ef4444;border:1px solid rgba(239,68,68,0.15)':'background:rgba(111,255,210,0.06);color:#6fffd2;border:1px solid rgba(111,255,210,0.15)'">
              {{ u.is_active ? 'Ban' : 'Activate' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage>1" class="flex gap-2 justify-center mt-8">
      <button v-for="p in lastPage" :key="p" @click="fetch(p)" class="w-9 h-9 rounded-xl text-xs font-semibold" :style="p===currentPage?'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b':'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)'">{{ p }}</button>
    </div>

    <!-- Edit Modal -->
    <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.75)" @click.self="editing=null">
      <div class="w-full max-w-md rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.25)">
        <h2 class="font-bold mb-5" style="color:#f0f6fc">Edit Partner</h2>
        <div class="space-y-3">
          <div>
            <label class="text-xs mb-1 block" style="color:#6e7f96">Name</label>
            <input v-model="editing.name" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
          </div>
          <div>
            <label class="text-xs mb-1 block" style="color:#6e7f96">Email</label>
            <input v-model="editing.email" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
          </div>
          <div>
            <label class="text-xs mb-1 block" style="color:#6e7f96">Role</label>
            <select v-model="editing.role" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
              <option value="agency">Agency</option>
              <option value="dealer">Dealer</option>
              <option value="seller">Seller</option>
            </select>
          </div>
          <div class="flex gap-4">
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc"><input type="checkbox" v-model="editing.is_active" /> Active</label>
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc"><input type="checkbox" v-model="editing.kyc_verified" /> KYC Verified</label>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="save" :disabled="saving" class="flex-1 py-3 rounded-xl font-bold text-sm disabled:opacity-50" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ saving?'Saving...':'Save Changes' }}</button>
          <button @click="editing=null" class="flex-1 py-3 rounded-xl text-sm" style="border:1px solid rgba(255,255,255,0.1);color:#c5d3e4">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import client from '@/api/client'
const items = ref<any[]>([])
const loading = ref(true)
const saving = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const filters = ref({ role: '', search: '' })
const editing = ref<any>(null)
let debounceTimer: any

onMounted(() => fetch(1))

async function fetch(page = 1) {
  loading.value = true
  try {
    const r = await client.get('/admin/agencies', { params: { ...filters.value, page } })
    items.value = r.data.data || []
    currentPage.value = r.data.current_page || 1
    lastPage.value = r.data.last_page || 1
    total.value = r.data.total || items.value.length
  } catch { items.value = [] }
  finally { loading.value = false }
}

function debounce() { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => fetch(1), 400) }
function openEdit(u: any) { editing.value = { ...u } }

async function save() {
  if (!editing.value) return
  saving.value = true
  try {
    await client.put('/admin/agencies/'+editing.value.id, editing.value)
    editing.value = null; fetch(currentPage.value)
  } catch(e: any) { alert(e?.response?.data?.message||'Failed') }
  finally { saving.value = false }
}

async function verify(id: string) { await client.post('/admin/agencies/'+id+'/verify'); fetch(currentPage.value) }
async function toggleActive(u: any) {
  await client.post('/admin/users/'+u.id+'/toggle-active')
  fetch(currentPage.value)
}
</script>
