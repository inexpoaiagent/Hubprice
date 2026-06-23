<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Page header -->
      <div class="mb-8">
        <div class="ai-badge mb-3">🚗 North Cyprus Auto Market</div>
        <h1 class="text-4xl font-black mb-2" style="color:#f0f6fc">Cars <span class="gradient-text">Marketplace</span></h1>
        <p class="text-sm" style="color:#6e7f96">{{ total }} verified listings with AI price analysis</p>
      </div>

      <!-- Filter bar -->
      <div class="rounded-2xl p-4 mb-8 flex flex-wrap items-center gap-3" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
        <input v-model="filters.search" type="text" placeholder="Search make, model..." class="input-dark flex-1 min-w-[160px]" @input="debouncedFetch" />
        <select v-model="filters.city" class="input-dark w-36" @change="fetchListings">
          <option value="">All Cities</option>
          <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
        </select>
        <select v-model="filters.fuel" class="input-dark w-32" @change="fetchListings">
          <option value="">Fuel Type</option>
          <option value="petrol">Petrol</option>
          <option value="diesel">Diesel</option>
          <option value="hybrid">Hybrid</option>
          <option value="electric">Electric</option>
        </select>
        <select v-model="filters.transmission" class="input-dark w-32" @change="fetchListings">
          <option value="">Transmission</option>
          <option value="automatic">Automatic</option>
          <option value="manual">Manual</option>
        </select>
        <select v-model="filters.sort" class="input-dark w-40" @change="fetchListings">
          <option value="newest">Newest First</option>
          <option value="price_asc">Price: Low → High</option>
          <option value="price_desc">Price: High → Low</option>
          <option value="trust">AI Trust Score</option>
        </select>
        <button @click="resetFilters" class="px-4 py-2.5 rounded-xl text-sm font-medium transition" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#6e7f96">Reset</button>
      </div>

      <!-- Listings grid -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div v-for="i in 8" :key="i" class="rounded-2xl h-72 animate-pulse" style="background:#0d1520"></div>
      </div>

      <div v-else-if="listings.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <ListingCard v-for="l in listings" :key="l.id" :listing="l" />
      </div>

      <div v-else class="text-center py-24">
        <p class="text-5xl mb-4">🔍</p>
        <p class="font-bold text-lg mb-2" style="color:#f0f6fc">No cars found</p>
        <p class="text-sm" style="color:#6e7f96">Try adjusting your filters</p>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="flex items-center justify-center gap-2 mt-10">
        <button v-for="p in pages" :key="p" @click="goPage(p)" :disabled="p === '...'" class="w-9 h-9 rounded-xl text-sm font-semibold transition" :style="p === currentPage ? 'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b' : p === '...' ? 'cursor:default;color:#6e7f96;background:transparent' : 'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)'">
          {{ p }}
        </button>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import ListingCard from '@/components/listings/ListingCard.vue'
import client from '@/api/client'
import type { Listing } from '@/types'

const listings = ref<Listing[]>([])
const loading = ref(true)
const total = ref(0)
const currentPage = ref(1)
const lastPage = ref(1)
let debounceTimer: ReturnType<typeof setTimeout>

const cities = ['Girne', 'Lefkosa', 'Gazimağusa', 'Iskele', 'Guzelyurt']
const filters = ref({ search: '', city: '', fuel: '', transmission: '', sort: 'newest' })

const pages = computed(() => {
  const p: (number | string)[] = []
  for (let i = 1; i <= lastPage.value; i++) {
    if (i === 1 || i === lastPage.value || Math.abs(i - currentPage.value) <= 2) p.push(i)
    else if (p[p.length - 1] !== '...') p.push('...')
  }
  return p
})

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchListings(), 400)
}

async function fetchListings(page = 1) {
  loading.value = true; currentPage.value = page
  try {
    const params: any = { type: 'vehicle', page, per_page: 12 }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.city) params.city = filters.value.city
    if (filters.value.fuel) params.fuel_type = filters.value.fuel
    if (filters.value.transmission) params.transmission = filters.value.transmission
    if (filters.value.sort === 'price_asc') { params.sort = 'price_asc' }
    else if (filters.value.sort === 'price_desc') { params.sort = 'price_desc' }
    else if (filters.value.sort === 'trust') { params.sort = 'ai_trust_score'; params.order = 'desc' }
    const { data } = await client.get('/listings', { params })
    listings.value = data.data ?? []
    total.value = data.total ?? 0
    lastPage.value = data.last_page ?? 1
  } catch { listings.value = [] } finally { loading.value = false }
}

function goPage(p: number | string) { if (typeof p === 'number') fetchListings(p) }
function resetFilters() { filters.value = { search: '', city: '', fuel: '', transmission: '', sort: 'newest' }; fetchListings() }

onMounted(() => fetchListings())
</script>
