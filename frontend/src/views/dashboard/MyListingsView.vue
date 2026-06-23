<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">My <span class="gradient-text">Listings</span></h1>
        <p class="text-sm" style="color:#6e7f96">{{ listings.length }} listings total</p>
      </div>
      <RouterLink to="/dashboard/listings/new" class="px-5 py-2.5 text-sm font-semibold rounded-xl" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">+ New Listing</RouterLink>
    </div>
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 4" :key="i" class="h-20 rounded-2xl animate-pulse" style="background:#0d1117"></div>
    </div>
    <div v-else-if="listings.length===0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <p class="text-4xl mb-3">📋</p>
      <p class="font-semibold mb-4" style="color:#f0f6fc">No listings yet</p>
      <RouterLink to="/dashboard/listings/new" class="px-5 py-2.5 text-sm font-semibold rounded-xl" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">Create First Listing</RouterLink>
    </div>
    <div v-else class="space-y-3">
      <div v-for="l in listings" :key="l.id" class="rounded-2xl p-5 flex items-center justify-between flex-wrap gap-4" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0" style="background:rgba(255,255,255,0.04)">
            <img v-if="l.thumbnail" :src="l.thumbnail" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-xl">{{ icon(l.type) }}</div>
          </div>
          <div>
            <p class="font-semibold text-sm" style="color:#f0f6fc">{{ l.title }}</p>
            <p class="text-xs mt-0.5" style="color:#6e7f96">{{ l.listing_number }} &middot; {{ l.city }} &middot; Views: {{ l.view_count||0 }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <span class="font-bold text-sm" style="color:#6fffd2">{{ fmt(l.price, l.currency) }}</span>
          <span class="text-xs px-2 py-0.5 rounded-full capitalize" :style="sty(l.status)">{{ l.status }}</span>
          <div class="flex gap-2">
            <RouterLink :to="'/listings/'+l.slug" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.08)">View</RouterLink>
            <RouterLink :to="'/dashboard/listings/'+l.id+'/edit'" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(86,216,255,0.08);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">Edit</RouterLink>
            <button @click="del(l.id)" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(239,68,68,0.06);color:#ef4444;border:1px solid rgba(239,68,68,0.15)">Delete</button>
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
const listings = ref<any[]>([])
const loading = ref(true)
onMounted(async () => { try { const r = await client.get('/my-listings'); listings.value = r.data.data || [] } catch {} finally { loading.value = false } })
async function del(id:string) { if (!confirm('Delete?')) return; await client.delete('/listings/'+id); listings.value = listings.value.filter(l=>l.id!==id) }
function fmt(v:number, cur='GBP') { return new Intl.NumberFormat('en-GB',{style:'currency',currency:cur||'GBP',maximumFractionDigits:0}).format(v) }
function sty(s:string) { const m:Record<string,string>={active:'background:rgba(111,255,210,0.1);color:#6fffd2',pending:'background:rgba(251,191,36,0.1);color:#fbbf24',rejected:'background:rgba(239,68,68,0.1);color:#ef4444'}; return m[s]||'background:rgba(255,255,255,0.05);color:#6e7f96' }
function icon(t:string) { return ({vehicle:'🚗',property:'🏠',rental:'🔑'} as any)[t]||'📋' }
</script>
