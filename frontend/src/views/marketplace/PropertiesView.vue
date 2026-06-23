<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="mb-8">
        <div class="ai-badge mb-3">🏠 North Cyprus Real Estate</div>
        <h1 class="text-4xl font-black mb-2" style="color:#f0f6fc">Properties <span class="gradient-text-violet">Marketplace</span></h1>
        <p class="text-sm" style="color:#6e7f96">{{ total }} verified properties with AI investment scoring</p>
      </div>
      <div class="rounded-2xl p-4 mb-8 flex flex-wrap items-center gap-3" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
        <input v-model="filters.search" type="text" placeholder="Search by title or location..." class="input-dark flex-1 min-w-[160px]" @input="debouncedFetch" />
        <select v-model="filters.city" class="input-dark w-36" @change="doFetch()">
          <option value="">All Cities</option>
          <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
        </select>
        <select v-model="filters.beds" class="input-dark w-28" @change="doFetch()">
          <option value="">Bedrooms</option>
          <option value="1">1+</option><option value="2">2+</option><option value="3">3+</option><option value="4">4+</option>
        </select>
        <select v-model="filters.sort" class="input-dark w-40" @change="doFetch()">
          <option value="newest">Newest First</option>
          <option value="price_asc">Price: Low → High</option>
          <option value="price_desc">Price: High → Low</option>
        </select>
        <button @click="reset" class="px-4 py-2.5 rounded-xl text-sm font-medium" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#6e7f96">Reset</button>
      </div>
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div v-for="i in 8" :key="i" class="rounded-2xl h-72 animate-pulse" style="background:#0d1520"></div>
      </div>
      <div v-else-if="listings.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <ListingCard v-for="l in listings" :key="l.id" :listing="l" />
      </div>
      <div v-else class="text-center py-24">
        <p class="text-5xl mb-4">🏠</p>
        <p class="font-bold text-lg mb-2" style="color:#f0f6fc">No properties found</p>
        <p class="text-sm" style="color:#6e7f96">Try adjusting your filters</p>
      </div>
      <div v-if="lastPage > 1" class="flex justify-center gap-2 mt-10">
        <button v-for="p in pages" :key="p" @click="goPage(p)" class="w-9 h-9 rounded-xl text-sm font-semibold transition" :style="p === currentPage ? 'background:linear-gradient(135deg,#56d8ff,#a78bfa);color:#05070b' : typeof p === 'string' ? 'cursor:default;color:#6e7f96' : 'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)'">{{ p }}</button>
      </div>
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import MainLayout from "@/layouts/MainLayout.vue"
import ListingCard from "@/components/listings/ListingCard.vue"
import client from "@/api/client"
import type { Listing } from "@/types"
const listings = ref<Listing[]>([]); const loading = ref(true); const total = ref(0); const currentPage = ref(1); const lastPage = ref(1)
let debounceTimer: ReturnType<typeof setTimeout>
const cities = ["Girne","Lefkosa","Gazimağusa","Iskele","Guzelyurt"]
const filters = ref({ search: "", city: "", beds: "", sort: "newest" })
const pages = computed(() => { const p: (number|string)[] = []; for (let i = 1; i <= lastPage.value; i++) { if (i===1||i===lastPage.value||Math.abs(i-currentPage.value)<=2) p.push(i); else if (p[p.length-1]!=="...") p.push("...") }; return p })
function debouncedFetch() { clearTimeout(debounceTimer); debounceTimer = setTimeout(doFetch, 400) }
async function doFetch(page = 1) {
  loading.value = true; currentPage.value = page
  try {
    const params: any = { type: "property", page, per_page: 12 }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.city) params.city = filters.value.city
    const { data } = await client.get("/listings", { params })
    listings.value = data.data ?? []; total.value = data.total ?? 0; lastPage.value = data.last_page ?? 1
  } catch { listings.value = [] } finally { loading.value = false }
}
function goPage(p: number|string) { if (typeof p === "number") doFetch(p) }
function reset() { filters.value = { search: "", city: "", beds: "", sort: "newest" }; doFetch() }
onMounted(doFetch)
</script>