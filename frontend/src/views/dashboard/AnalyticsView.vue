<template>
  <div class="p-8">
    <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Listing <span class="gradient-text">Analytics</span></h1>
    <p class="text-sm mb-8" style="color:#6e7f96">Performance metrics for your listings</p>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="s in stats" :key="s.label" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <p class="text-2xl font-black mb-1" :style="{color:s.color}">{{ s.value }}</p>
        <p class="text-xs" style="color:#6e7f96">{{ s.label }}</p>
      </div>
    </div>
    <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <h2 class="font-bold mb-5" style="color:#f0f6fc">Listing Performance</h2>
      <div v-if="loading" class="space-y-2"><div v-for="i in 3" :key="i" class="h-10 rounded-lg animate-pulse" style="background:rgba(255,255,255,0.04)"></div></div>
      <div v-else-if="listings.length===0" class="text-center py-8 text-sm" style="color:#6e7f96">No listings yet</div>
      <table v-else class="w-full">
        <thead><tr class="text-xs" style="color:#6e7f96;border-bottom:1px solid rgba(255,255,255,0.06)"><th class="text-left pb-3 font-medium">Listing</th><th class="text-left pb-3 font-medium">Views</th><th class="text-left pb-3 font-medium">Inquiries</th><th class="text-left pb-3 font-medium">AI Score</th></tr></thead>
        <tbody>
          <tr v-for="l in listings" :key="l.id" class="text-xs border-b" style="border-color:rgba(255,255,255,0.04)">
            <td class="py-3" style="color:#f0f6fc">{{ l.title }}</td>
            <td class="py-3" style="color:#c5d3e4">{{ l.view_count||0 }}</td>
            <td class="py-3" style="color:#c5d3e4">{{ l.inquiry_count||0 }}</td>
            <td class="py-3"><span v-if="l.price_battery_percent" class="font-bold" :style="l.price_battery_percent>=85?'color:#6fffd2':l.price_battery_percent>=60?'color:#fbbf24':'color:#ef4444'">{{ l.price_battery_percent }}%</span><span v-else style="color:#6e7f96">-</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import client from '@/api/client'
const listings = ref<any[]>([]); const loading = ref(true)
onMounted(async () => { try { const r = await client.get('/my-listings?per_page=50'); listings.value = r.data.data || [] } catch {} finally { loading.value=false } })
const stats = computed(() => [
  { label:'Total Views', value: listings.value.reduce((s:number,l:any)=>s+(l.view_count||0),0), color:'#6fffd2' },
  { label:'Inquiries', value: listings.value.reduce((s:number,l:any)=>s+(l.inquiry_count||0),0), color:'#56d8ff' },
  { label:'Avg AI Score', value: listings.value.length ? Math.round(listings.value.reduce((s:number,l:any)=>s+Number(l.price_battery_percent||0),0)/listings.value.length)+'%' : '-', color:'#a78bfa' },
  { label:'Featured', value: listings.value.filter((l:any)=>l.is_featured).length, color:'#fbbf24' },
])
</script>
