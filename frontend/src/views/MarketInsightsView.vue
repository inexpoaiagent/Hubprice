<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-16">
        <span class="ai-badge mb-4 inline-block">Market Pulse Engine™</span>
        <h1 class="text-5xl font-black text-white mb-4">Market <span class="gradient-text">Intelligence</span></h1>
        <p class="text-slate-400 text-xl">Real-time AI-powered market analytics and trends</p>
      </div>
      <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div v-for="s in statsCards" :key="s.label" class="glass rounded-2xl p-6 text-center">
          <div class="text-3xl font-black mb-1" :class="s.color">{{ s.value }}</div>
          <div class="text-sm text-slate-400">{{ s.label }}</div>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
          <h3 class="font-bold text-white mb-4">Market Trust Index™</h3>
          <div class="space-y-4">
            <div v-for="m in marketTrust" :key="m.label" class="flex items-center gap-3">
              <span class="text-sm text-slate-400 w-32">{{ m.label }}</span>
              <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all" :class="m.color" :style="{ width: m.value + '%' }"></div>
              </div>
              <span class="text-sm font-semibold text-white w-10 text-right">{{ m.value }}%</span>
            </div>
          </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
          <h3 class="font-bold text-white mb-4">AI Competitor Intelligence</h3>
          <div class="space-y-3">
            <div v-for="source in competitorSources" :key="source.name" class="flex items-center justify-between p-3 bg-slate-800 rounded-xl">
              <div>
                <p class="text-sm font-medium text-white">{{ source.name }}</p>
                <p class="text-xs text-slate-500">{{ source.type }}</p>
              </div>
              <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded-full">Active</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'; import MainLayout from '@/layouts/MainLayout.vue'; import { useMarketStore } from '@/stores/market'; import client from '@/api/client'
const marketStore = useMarketStore(); const stats = ref<any>(null)
onMounted(async () => { await marketStore.fetchStats(); stats.value = marketStore.stats })
const statsCards = computed(() => stats.value ? [
  { label: 'Vehicle Listings', value: stats.value.vehicle_listings?.toLocaleString(), color: 'text-sky-400' },
  { label: 'Property Listings', value: stats.value.property_listings?.toLocaleString(), color: 'text-violet-400' },
  { label: 'Rental Listings', value: stats.value.rental_listings?.toLocaleString(), color: 'text-emerald-400' },
  { label: 'AI Analyses', value: stats.value.total_listings?.toLocaleString(), color: 'text-amber-400' },
] : [])
const marketTrust = [
  { label: 'Cars Market', value: 87, color: 'bg-sky-500' },
  { label: 'Properties', value: 92, color: 'bg-violet-500' },
  { label: 'Rentals', value: 78, color: 'bg-emerald-500' },
  { label: 'Overall Trust', value: 85, color: 'bg-amber-500' },
]
const competitorSources = [
  { name: 'KKT Carabam', type: 'Vehicle Market' },
  { name: 'Hangiev', type: 'Property Market' },
  { name: 'Noyanlar Invest', type: 'Investment Market' },
]
</script>