<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Listing <span class="gradient-text">Management</span></h1>
        <p class="text-sm" style="color:#6e7f96">Approve, reject, edit and manage all listings</p>
      </div>
      <span class="px-3 py-1 rounded-full text-sm" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">{{ total }} listings</span>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6 p-4 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <select v-model="filters.status" @change="fetchListings(1)" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
        <option value="">All Status</option>
        <option value="pending">Pending Review</option>
        <option value="active">Active</option>
        <option value="rejected">Rejected</option>
        <option value="sold">Sold</option>
        <option value="expired">Expired</option>
      </select>
      <select v-model="filters.type" @change="fetchListings(1)" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
        <option value="">All Types</option>
        <option value="vehicle">Vehicles</option>
        <option value="property">Properties</option>
        <option value="rental">Rentals</option>
      </select>
      <input v-model="filters.search" @input="debounce" placeholder="Search title, number, city..." class="px-4 py-2 rounded-xl text-sm flex-1 min-w-48" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 6" :key="i" class="h-20 rounded-2xl animate-pulse" style="background:#0d1117"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="listings.length===0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:rgba(111,255,210,0.08);border:1px solid rgba(111,255,210,0.15)">
        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
      </div>
      <p class="font-semibold" style="color:#f0f6fc">No listings found</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <div v-for="l in listings" :key="l.id" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0" style="background:rgba(255,255,255,0.04)">
              <img v-if="l.thumbnail" :src="l.thumbnail" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-xl"><span v-if="l.type==='vehicle'" style="display:inline-flex;align-items:center"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6H5.5A2.5 2.5 0 003 8.5v2A1.5 1.5 0 004.5 12h15a1.5 1.5 0 001.5-1.5v-2A2.5 2.5 0 0018.5 6H16m-8 0l2-3h4l2 3M8 6h8M6 12v5a1 1 0 001 1h1m8-6v5a1 1 0 01-1 1h-1m-6 0h6"/></svg></span><span v-else-if="l.type==='property'" style="display:inline-flex;align-items:center"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg></span><span v-else style="display:inline-flex;align-items:center"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#56d8ff" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span></div>
            </div>
            <div>
              <p class="font-semibold text-sm mb-0.5" style="color:#f0f6fc">{{ l.title }}</p>
              <p class="text-xs" style="color:#6e7f96">{{ l.user?.name }} · {{ l.city || '-' }} · #{{ l.listing_number }}</p>
              <p class="text-xs mt-0.5" style="color:#6e7f96">{{ l.type }} · {{ l.created_at ? new Date(l.created_at).toLocaleDateString('en-GB') : '' }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="font-bold text-sm" style="color:#6fffd2">{{ fmt(l.price, l.currency) }}</span>
            <div v-if="l.price_battery_percent != null" class="flex items-center gap-1">
              <div class="w-10 h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.08)">
                <div class="h-full rounded-full" :style="'width:'+l.price_battery_percent+'%;background:'+(l.price_battery_percent>=80?'#6fffd2':l.price_battery_percent>=50?'#fbbf24':'#ef4444')"></div>
              </div>
              <span class="text-xs font-bold" :style="'color:'+(l.price_battery_percent>=80?'#6fffd2':l.price_battery_percent>=50?'#fbbf24':'#ef4444')">{{ l.price_battery_percent }}%</span>
            </div>
            <span class="text-xs px-2 py-1 rounded-full capitalize font-medium" :style="statusStyle(l.status)">{{ l.status }}</span>
            <span v-if="l.is_featured" class="text-xs px-2 py-1 rounded-full font-medium flex items-center gap-1" style="background:rgba(251,191,36,0.1);color:#fbbf24"><svg width="10" height="10" fill="#fbbf24" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg> Featured</span>
            <span v-if="l.is_premium" class="text-xs px-2 py-1 rounded-full font-medium" style="background:rgba(167,139,250,0.1);color:#a78bfa">Premium</span>
            <button v-if="l.status==='pending'" @click="approve(l.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">Approve</button>
            <button v-if="l.status==='pending'" @click="reject(l.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(239,68,68,0.08);color:#ef4444;border:1px solid rgba(239,68,68,0.2)">Reject</button>
            <button @click="feature(l.id)" class="px-3 py-1.5 text-xs rounded-lg flex items-center gap-1" :style="l.is_featured?'background:rgba(251,191,36,0.1);color:#fbbf24;border:1px solid rgba(251,191,36,0.2)':'background:rgba(255,255,255,0.04);color:#6e7f96;border:1px solid rgba(255,255,255,0.08)'">
              <svg width="10" height="10" :fill="l.is_featured?'#fbbf24':'none'" viewBox="0 0 24 24" :stroke="l.is_featured?'#fbbf24':'#6e7f96'" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              {{ l.is_featured ? 'Unfeature' : 'Feature' }}
            </button>
            <button @click="openEdit(l)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(86,216,255,0.08);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">Edit</button>
            <button @click="reanalyze(l.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(167,139,250,0.08);color:#a78bfa;border:1px solid rgba(167,139,250,0.2)">AI</button>
            <button @click="del(l.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(239,68,68,0.06);color:#ef4444;border:1px solid rgba(239,68,68,0.15)">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage>1" class="flex gap-2 justify-center mt-8">
      <button v-for="p in lastPage" :key="p" @click="fetchListings(p)" class="w-9 h-9 rounded-xl text-xs font-semibold" :style="p===currentPage?'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b':'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)'">{{ p }}</button>
    </div>

    <!-- Edit Modal -->
    <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.75)" @click.self="editing=null">
      <div class="w-full max-w-lg rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.25)">
        <h2 class="font-bold text-lg mb-5" style="color:#f0f6fc">Edit Listing</h2>
        <div class="space-y-3">
          <div>
            <label class="text-xs mb-1 block" style="color:#6e7f96">Title</label>
            <input v-model="editing.title" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">Price</label>
              <input v-model="editing.price" type="number" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
            </div>
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">Currency</label>
              <select v-model="editing.currency" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
                <option>GBP</option><option>USD</option><option>EUR</option><option>TRY</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">City</label>
              <input v-model="editing.city" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
            </div>
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">Status</label>
              <select v-model="editing.status" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
                <option value="sold">Sold</option>
                <option value="expired">Expired</option>
              </select>
            </div>
          </div>
          <div>
            <label class="text-xs mb-1 block" style="color:#6e7f96">Description</label>
            <textarea v-model="editing.description" rows="3" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc"></textarea>
          </div>
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc">
              <input type="checkbox" v-model="editing.is_featured" class="rounded" /> Featured
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc">
              <input type="checkbox" v-model="editing.is_premium" class="rounded" /> Premium
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc">
              <input type="checkbox" v-model="editing.price_negotiable" class="rounded" /> Negotiable
            </label>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="saveEdit" :disabled="saving" class="flex-1 py-3 rounded-xl font-bold text-sm disabled:opacity-50" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ saving?'Saving...':'Save Changes' }}</button>
          <button @click="editing=null" class="flex-1 py-3 rounded-xl text-sm" style="border:1px solid rgba(255,255,255,0.1);color:#c5d3e4">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import client from '@/api/client'
const listings = ref<any[]>([])
const loading = ref(true)
const saving = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const filters = ref({ status: '', type: '', search: '' })
const editing = ref<any>(null)
let debounceTimer: any

onMounted(() => fetchListings(1))

async function fetchListings(page = 1) {
  loading.value = true
  try {
    const r = await client.get('/admin/listings', { params: { ...filters.value, page } })
    listings.value = r.data.data || []
    currentPage.value = r.data.current_page || 1
    lastPage.value = r.data.last_page || 1
    total.value = r.data.total || listings.value.length
  } catch { listings.value = [] }
  finally { loading.value = false }
}

function debounce() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchListings(1), 400)
}

function openEdit(l: any) { editing.value = { ...l } }

async function saveEdit() {
  if (!editing.value) return
  saving.value = true
  try {
    await client.put('/admin/listings/' + editing.value.id, editing.value)
    editing.value = null
    fetchListings(currentPage.value)
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Save failed')
  } finally { saving.value = false }
}

async function approve(id: string) { await client.post('/admin/listings/'+id+'/approve'); fetchListings(currentPage.value) }
async function reject(id: string) {
  const reason = prompt('Rejection reason (required):')
  if (!reason) return
  await client.post('/admin/listings/'+id+'/reject', { reason })
  fetchListings(currentPage.value)
}
async function feature(id: string) { await client.post('/admin/listings/'+id+'/feature'); fetchListings(currentPage.value) }
async function reanalyze(id: string) { await client.post('/admin/listings/'+id+'/reanalyze'); fetchListings(currentPage.value) }
async function del(id: string) { if (!confirm('Delete this listing permanently?')) return; await client.delete('/admin/listings/'+id); fetchListings(currentPage.value) }

function statusStyle(s: string) {
  const m: Record<string,string> = { active:'background:rgba(111,255,210,0.1);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)', pending:'background:rgba(251,191,36,0.1);color:#fbbf24;border:1px solid rgba(251,191,36,0.2)', rejected:'background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.2)', sold:'background:rgba(86,216,255,0.1);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)', expired:'background:rgba(255,255,255,0.05);color:#6e7f96;border:1px solid rgba(255,255,255,0.1)' }
  return m[s] || m.expired
}
function fmt(v: number, c = 'GBP') { return new Intl.NumberFormat('en-GB', { style:'currency', currency:c||'GBP', maximumFractionDigits:0 }).format(v) }
function typeIcon(_t: string) { return '' }
</script>