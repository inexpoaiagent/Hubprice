<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">User <span class="gradient-text">Management</span></h1>
        <p class="text-sm" style="color:#6e7f96">Manage users, KYC verification, and account status</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6 p-4 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <input v-model="search" @input="debouncedFetch" placeholder="Search by name or email..."
        class="px-4 py-2 rounded-xl text-sm flex-1 min-w-56" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
      <select v-model="roleFilter" @change="fetchUsers"
        class="px-4 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
        <option value="">All Roles</option>
        <option v-for="r in roles" :key="r" :value="r" class="capitalize">{{ r }}</option>
      </select>
      <select v-model="kycFilter" @change="fetchUsers"
        class="px-4 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
        <option value="">KYC: All</option>
        <option value="1">KYC Verified</option>
        <option value="0">KYC Pending</option>
      </select>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 6" :key="i" class="rounded-2xl h-16 animate-pulse" style="background:#0d1117"></div>
    </div>

    <!-- Empty state -->
    <div v-else-if="users.length === 0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <div class="text-5xl mb-3">👥</div>
      <p class="font-semibold" style="color:#f0f6fc">No users found</p>
    </div>

    <!-- Users list -->
    <div v-else class="space-y-3">
      <div v-for="u in users" :key="u.id" class="rounded-2xl p-4 flex items-center justify-between flex-wrap gap-4"
        style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <!-- Avatar + info -->
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
            style="background:linear-gradient(135deg,rgba(111,255,210,0.2),rgba(86,216,255,0.2));color:#6fffd2">
            {{ u.name?.charAt(0)?.toUpperCase() || '?' }}
          </div>
          <div>
            <p class="font-semibold text-sm" style="color:#f0f6fc">{{ u.name }}</p>
            <p class="text-xs" style="color:#6e7f96">{{ u.email }}</p>
          </div>
        </div>

        <!-- Badges + Actions -->
        <div class="flex items-center gap-3 flex-wrap">
          <span class="text-xs px-2 py-1 rounded-full capitalize font-medium"
            style="background:rgba(86,216,255,0.1);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">
            {{ u.role }}
          </span>
          <span class="text-xs px-2 py-1 rounded-full font-medium"
            :style="u.kyc_verified ? 'background:rgba(111,255,210,0.1);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)' : 'background:rgba(255,255,255,0.04);color:#6e7f96;border:1px solid rgba(255,255,255,0.08)'">
            {{ u.kyc_verified ? '✓ KYC Verified' : 'KYC Pending' }}
          </span>
          <span class="text-xs px-2 py-1 rounded-full font-medium"
            :style="u.is_active !== false ? 'background:rgba(111,255,210,0.08);color:#6fffd2' : 'background:rgba(239,68,68,0.08);color:#ef4444'">
            {{ u.is_active !== false ? 'Active' : 'Suspended' }}
          </span>

          <!-- Risk score if present -->
          <span v-if="u.risk_score !== undefined && u.risk_score !== null"
            class="text-xs px-2 py-1 rounded-full font-medium"
            :style="`background:rgba(${u.risk_score>70?'239,68,68':u.risk_score>40?'251,191,36':'111,255,210'},0.08);color:${u.risk_score>70?'#ef4444':u.risk_score>40?'#fbbf24':'#6fffd2'}`">
            Risk: {{ u.risk_score }}%
          </span>

          <div class="flex gap-2">
            <button v-if="!u.kyc_verified" @click="approveKyc(u.id)"
              class="px-3 py-1.5 text-xs rounded-lg font-medium transition-all"
              style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">
              Approve KYC
            </button>
            <button @click="toggleActive(u.id, u.is_active)"
              class="px-3 py-1.5 text-xs rounded-lg font-medium transition-all"
              :style="u.is_active !== false ? 'background:rgba(251,191,36,0.08);color:#fbbf24;border:1px solid rgba(251,191,36,0.2)' : 'background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)'">
              {{ u.is_active !== false ? 'Suspend' : 'Activate' }}
            </button>
            <button @click="changeRole(u)"
              class="px-3 py-1.5 text-xs rounded-lg font-medium transition-all"
              style="background:rgba(167,139,250,0.08);color:#a78bfa;border:1px solid rgba(167,139,250,0.2)">
              Change Role
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" class="flex items-center justify-center gap-2 mt-8">
      <button v-for="p in lastPage" :key="p" @click="goPage(p)"
        class="w-9 h-9 rounded-xl text-xs font-semibold transition-all"
        :style="p === currentPage ? 'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b' : 'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)'">
        {{ p }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import client from '@/api/client'

const users = ref<any[]>([])
const loading = ref(true)
const search = ref('')
const roleFilter = ref('')
const kycFilter = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const roles = ['buyer', 'seller', 'dealer', 'agency', 'investor', 'moderator', 'support', 'admin']
let debounceTimer: ReturnType<typeof setTimeout>

async function fetchUsers(page = 1) {
  loading.value = true
  try {
    const r = await client.get('/admin/users', {
      params: { search: search.value, role: roleFilter.value, kyc_verified: kycFilter.value, page }
    })
    users.value = r.data.data || []
    currentPage.value = r.data.current_page || 1
    lastPage.value = r.data.last_page || 1
  } catch {
    users.value = []
  } finally {
    loading.value = false
  }
}

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchUsers(1), 400)
}

function goPage(p: number) { fetchUsers(p) }

onMounted(() => fetchUsers())

async function approveKyc(id: string) {
  await client.post(`/admin/users/${id}/kyc-approve`)
  fetchUsers(currentPage.value)
}

async function toggleActive(id: string, currentlyActive: boolean) {
  await client.post()
  fetchUsers(currentPage.value)
}

async function changeRole(u: any) {
  const newRole = prompt(`Change role for ${u.name}\nCurrent: ${u.role}\nNew role:`, u.role)
  if (!newRole || newRole === u.role) return
  await client.put(`/admin/users/${u.id}`, { role: newRole })
  fetchUsers(currentPage.value)
}
</script>
