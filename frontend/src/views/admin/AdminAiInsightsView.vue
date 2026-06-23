<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <div class="ai-badge mb-2">⬡ AI Intelligence Center</div>
        <h1 class="text-3xl font-black" style="color:#f0f6fc">AI Market <span class="gradient-text">Insights</span></h1>
      </div>
      <button class="btn-primary px-5 py-2.5 text-sm">Regenerate Analysis</button>
    </div>

    <!-- Market Trust Index -->
    <div class="rounded-2xl p-6 mb-6" style="background:linear-gradient(135deg,rgba(111,255,210,0.05),rgba(86,216,255,0.03));border:1px solid rgba(111,255,210,0.2)">
      <h2 class="font-bold text-lg mb-5 flex items-center gap-2" style="color:#f0f6fc">
        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">AI</div>
        Market Trust Index™
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div v-for="mkt in marketTrust" :key="mkt.label" class="rounded-xl p-5" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07)">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold" style="color:#c5d3e4">{{ mkt.label }}</span>
            <span class="text-xl font-black" :style="{color: mkt.color}">{{ mkt.score }}</span>
          </div>
          <div class="space-y-2">
            <div v-for="sub in mkt.breakdown" :key="sub.name" class="flex items-center gap-2">
              <span class="text-xs w-20 truncate" style="color:#6e7f96">{{ sub.name }}</span>
              <div class="flex-1 h-1.5 rounded-full" style="background:rgba(255,255,255,0.07)">
                <div class="h-1.5 rounded-full" :style="`width:${sub.val}%;background:${mkt.color}`"></div>
              </div>
              <span class="text-xs w-6 text-right" :style="{color: mkt.color}">{{ sub.val }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Undervalued Assets -->
      <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.15)">
        <h2 class="font-bold mb-5 flex items-center gap-2" style="color:#f0f6fc">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Undervalued Assets
        </h2>
        <div class="space-y-3">
          <div v-for="a in undervalued" :key="a.title" class="flex items-center gap-3 p-3 rounded-xl" style="background:rgba(111,255,210,0.04);border:1px solid rgba(111,255,210,0.1)">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :style="a.type==='vehicle'?'background:rgba(111,255,210,0.12)':'background:rgba(167,139,250,0.12)'">
              <svg v-if="a.type==='vehicle'" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6H5.5A2.5 2.5 0 003 8.5v2A1.5 1.5 0 004.5 12h15a1.5 1.5 0 001.5-1.5v-2A2.5 2.5 0 0018.5 6H16m-8 0l2-3h4l2 3M8 6h8M6 12v5a1 1 0 001 1h1m8-6v5a1 1 0 01-1 1h-1m-6 0h6"/></svg>
              <svg v-else width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold truncate" style="color:#f0f6fc">{{ a.title }}</p>
              <p class="text-xs" style="color:#6e7f96">Listed: {{ a.listed }} · Market avg: {{ a.avg }}</p>
            </div>
            <span class="text-xs font-black px-2 py-1 rounded-lg whitespace-nowrap" style="background:rgba(111,255,210,0.12);color:#6fffd2">{{ a.diff }} below</span>
          </div>
        </div>
      </div>

      <!-- Overpriced Assets -->
      <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(239,68,68,0.15)">
        <h2 class="font-bold mb-5 flex items-center gap-2" style="color:#f0f6fc">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          Overpriced Listings
        </h2>
        <div class="space-y-3">
          <div v-for="a in overpriced" :key="a.title" class="flex items-center gap-3 p-3 rounded-xl" style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.1)">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :style="a.type==='vehicle'?'background:rgba(111,255,210,0.12)':'background:rgba(167,139,250,0.12)'">
              <svg v-if="a.type==='vehicle'" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6H5.5A2.5 2.5 0 003 8.5v2A1.5 1.5 0 004.5 12h15a1.5 1.5 0 001.5-1.5v-2A2.5 2.5 0 0018.5 6H16m-8 0l2-3h4l2 3M8 6h8M6 12v5a1 1 0 001 1h1m8-6v5a1 1 0 01-1 1h-1m-6 0h6"/></svg>
              <svg v-else width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold truncate" style="color:#f0f6fc">{{ a.title }}</p>
              <p class="text-xs" style="color:#6e7f96">Listed: {{ a.listed }} · Market avg: {{ a.avg }}</p>
            </div>
            <span class="text-xs font-black px-2 py-1 rounded-lg whitespace-nowrap" style="background:rgba(239,68,68,0.12);color:#ef4444">{{ a.diff }} above</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Demand Analysis + Fast Selling -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <h2 class="font-bold mb-5 flex items-center gap-2" style="color:#f0f6fc">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#56d8ff" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          Demand by Category
        </h2>
        <div class="space-y-4">
          <div v-for="d in demand" :key="d.label">
            <div class="flex justify-between text-xs mb-1.5">
              <span style="color:#c5d3e4">{{ d.label }}</span>
              <span :style="{color: d.color}" class="font-bold">{{ d.score }}/100</span>
            </div>
            <div class="h-2.5 rounded-full" style="background:rgba(255,255,255,0.07)">
              <div class="h-2.5 rounded-full transition-all" :style="`width:${d.score}%;background:${d.color}`"></div>
            </div>
            <p class="text-xs mt-1" style="color:#6e7f96">{{ d.note }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <h2 class="font-bold mb-5 flex items-center gap-2" style="color:#f0f6fc">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          Fast Selling Indicators
        </h2>
        <div class="space-y-3">
          <div v-for="f in fastSelling" :key="f.type" class="p-3 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05)">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-bold" style="color:#f0f6fc">{{ f.type }}</p>
                <p class="text-xs" style="color:#6e7f96">Avg days on market: {{ f.days }}</p>
              </div>
              <span class="text-xs px-2 py-1 rounded-full font-bold" :style="`background:rgba(${f.hot ? '111,255,210' : '86,216,255'},0.1);color:${f.hot ? '#6fffd2' : '#56d8ff'}`">
                <svg v-if="f.hot" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                <svg v-else width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                {{ f.hot ? 'Hot' : 'Rising' }}
              </span>
            </div>
            <div class="mt-2 h-1.5 rounded-full" style="background:rgba(255,255,255,0.07)">
              <div class="h-1.5 rounded-full" :style="`width:${f.pct}%;background:${f.hot ? '#6fffd2' : '#56d8ff'}`"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const marketTrust = [
  { label: 'Cars Market', score: 84, color: '#6fffd2', breakdown: [{ name: 'Confidence', val: 88 }, { name: 'Demand', val: 82 }, { name: 'Growth', val: 79 }, { name: 'Liquidity', val: 87 }] },
  { label: 'Properties Market', score: 76, color: '#56d8ff', breakdown: [{ name: 'Confidence', val: 78 }, { name: 'Demand', val: 81 }, { name: 'Growth', val: 84 }, { name: 'Liquidity', val: 61 }] },
  { label: 'Rental Market', score: 91, color: '#a78bfa', breakdown: [{ name: 'Confidence', val: 94 }, { name: 'Demand', val: 96 }, { name: 'Growth', val: 88 }, { name: 'Liquidity', val: 86 }] },
]
const undervalued = [
  { type: 'vehicle', title: '2020 Toyota RAV4 — Lefkosa', listed: '435,000 TRY', avg: '480,000 TRY', diff: '9.4%' },
  { type: 'property', title: '2BR Apt Famagusta Near Uni', listed: '£55,000', avg: '£68,000', diff: '19.1%' },
  { type: 'vehicle', title: '2019 Honda Civic — Iskele', listed: '295,000 TRY', avg: '320,000 TRY', diff: '7.8%' },
]
const overpriced = [
  { type: 'vehicle', title: '2018 Mercedes E-Class — Girne', listed: '650,000 TRY', avg: '580,000 TRY', diff: '12.1%' },
  { type: 'property', title: '6BR Villa with Pool — Kyrenia', listed: '£750,000', avg: '£680,000', diff: '10.3%' },
  { type: 'vehicle', title: '2019 BMW X3 — Lefkosa', listed: '590,000 TRY', avg: '545,000 TRY', diff: '8.3%' },
]
const demand = [
  { label: 'SUVs & Crossovers', score: 92, color: '#6fffd2', note: 'High demand, avg 8 days to sell' },
  { label: 'Sea View Properties', score: 88, color: '#56d8ff', note: 'Consistent demand from UK buyers' },
  { label: 'Long Beach Area', score: 85, color: '#a78bfa', note: 'Investment hot spot, rising prices' },
  { label: 'Electric Vehicles', score: 43, color: '#fbbf24', note: 'Low supply, growing interest' },
  { label: 'Commercial Properties', score: 38, color: '#6e7f96', note: 'Slow market, excess supply' },
]
const fastSelling = [
  { type: 'Toyota/Honda Under 400K TRY', days: '6.2', pct: 95, hot: true },
  { type: 'Furnished Apts Kyrenia', days: '12.4', pct: 82, hot: true },
  { type: 'Hybrid Cars Any Brand', days: '9.8', pct: 78, hot: false },
  { type: '2BR Apartments £50-90K', days: '18.1', pct: 65, hot: false },
]
</script>
