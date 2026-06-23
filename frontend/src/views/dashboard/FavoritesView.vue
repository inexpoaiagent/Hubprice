<template>
  <div class="p-8">
    <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Saved <span class="gradient-text">Listings</span></h1>
    <p class="text-sm mb-8" style="color:#6e7f96">Properties and vehicles you have saved</p>
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="i in 6" :key="i" class="h-48 rounded-2xl animate-pulse" style="background:#0d1117"></div>
    </div>
    <div v-else-if="favs.length===0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <p class="text-4xl mb-3">❤️</p>
      <p class="font-semibold" style="color:#f0f6fc">No saved listings</p>
      <p class="text-sm mt-1 mb-5" style="color:#6e7f96">Heart listings you like to save them here</p>
      <RouterLink to="/cars" class="px-5 py-2.5 text-sm font-semibold rounded-xl" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">Browse Listings</RouterLink>
    </div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="l in favs" :key="l.id" class="rounded-2xl overflow-hidden" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="h-36 overflow-hidden" style="background:rgba(255,255,255,0.04)"><img v-if="l.thumbnail" :src="l.thumbnail" class="w-full h-full object-cover" /></div>
        <div class="p-4">
          <p class="font-semibold text-sm mb-1" style="color:#f0f6fc">{{ l.title }}</p>
          <p class="text-xs mb-3" style="color:#6e7f96">{{ l.city }}</p>
          <div class="flex items-center justify-between">
            <span class="font-bold text-sm" style="color:#6fffd2">{{ fmt(l.price, l.currency) }}</span>
            <RouterLink :to="'/listings/'+l.slug" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">View</RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import client from '@/api/client'
const favs = ref<any[]>([]); const loading = ref(true)
onMounted(async () => { try { const r = await client.get('/favorites'); favs.value = r.data.data || r.data || [] } catch {} finally { loading.value = false } })
function fmt(v:number, cur='GBP') { return new Intl.NumberFormat('en-GB',{style:'currency',currency:cur||'GBP',maximumFractionDigits:0}).format(v) }
</script>
