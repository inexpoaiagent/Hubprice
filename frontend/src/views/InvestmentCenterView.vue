<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-16">
        <span class="ai-badge mb-4 inline-block">Investment Center</span>
        <h1 class="text-5xl font-black text-white mb-4">AI <span class="gradient-text">Investment</span> Intelligence</h1>
        <p class="text-slate-400 text-xl">Discover high-ROI opportunities powered by AI Investment Score™</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div v-for="card in investCards" :key="card.title" class="rounded-2xl p-6 border" :style="{ background: card.bg, borderColor: card.border }">
          <div class="text-4xl mb-4">{{ card.icon }}</div>
          <h3 class="text-xl font-black text-white mb-2">{{ card.title }}</h3>
          <p class="text-slate-400 text-sm">{{ card.desc }}</p>
        </div>
      </div>
      <div class="glass rounded-2xl p-8 text-center">
        <h2 class="text-3xl font-black text-white mb-4">Find Investment Opportunities</h2>
        <p class="text-slate-400 mb-6">Browse properties and vehicles with high AI Investment Scores to find your next opportunity.</p>
        <div class="flex gap-4 justify-center">
          <RouterLink to="/properties" class="px-6 py-3 bg-gradient-to-r from-sky-500 to-violet-600 text-white font-semibold rounded-xl hover:opacity-90">Property Investments</RouterLink>
          <RouterLink to="/cars" class="px-6 py-3 border border-slate-700 text-white font-semibold rounded-xl hover:border-sky-500 transition">Vehicle Investments</RouterLink>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import { RouterLink } from 'vue-router'; import MainLayout from '@/layouts/MainLayout.vue'
const investCards = [
  { icon: '🏠', title: 'Property ROI', desc: 'AI predicts rental yields, capital growth, and buy-to-let returns for every property listing.', bg: 'rgba(139,92,246,0.1)', border: 'rgba(139,92,246,0.3)' },
  { icon: '🚗', title: 'Vehicle Appreciation', desc: 'Identify classic cars and collector vehicles with strong appreciation potential using AI.', bg: 'rgba(14,165,233,0.1)', border: 'rgba(14,165,233,0.3)' },
  { icon: '📈', title: 'Market Timing', desc: 'Get AI-powered signals on when to buy, hold, or sell based on real-time market conditions.', bg: 'rgba(16,185,129,0.1)', border: 'rgba(16,185,129,0.3)' },
]
</script>