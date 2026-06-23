<template>
  <MainLayout>
    <!-- HERO -->
    <section class="relative overflow-hidden pt-20 pb-24 px-4">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] pointer-events-none" style="background:radial-gradient(ellipse 70% 60% at 50% 0%,rgba(111,255,210,0.08) 0%,transparent 70%)"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold mb-6" style="background:rgba(111,255,210,0.08);border:1px solid rgba(111,255,210,0.2);color:#6fffd2">
            <span class="w-1.5 h-1.5 rounded-full" style="background:#6fffd2;animation:pulse-glow 2s infinite"></span>
            AI-Powered Marketplace · North Cyprus
          </div>
          <h1 class="text-5xl sm:text-6xl font-black leading-none mb-6" style="color:#f0f6fc">
            Find the Best <span class="gradient-text">Priced</span><br/>Cars &amp; Properties
          </h1>
          <p class="text-lg max-w-2xl mx-auto mb-10" style="color:#6e7f96">
            AI-verified prices on every car and property listing. Know the real market value before you buy or sell.
          </p>

          <!-- Search Box -->
          <div class="max-w-3xl mx-auto">
            <div class="flex p-1.5 rounded-2xl mb-3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(111,255,210,0.15)">
              <select v-model="searchType" class="px-4 py-2.5 rounded-xl text-sm font-semibold mr-2 outline-none" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);color:#f0f6fc;min-width:120px">
                <option value="">All</option>
                <option value="vehicle">Cars</option>
                <option value="property">Property</option>
              </select>
              <input v-model="searchQuery" type="text"
                placeholder="Search by city, brand, model, type..."
                class="flex-1 bg-transparent outline-none text-sm px-3" style="color:#f0f6fc"
                @keydown.enter="doSearch" />
              <select v-model="searchCity" class="px-3 py-2.5 rounded-xl text-sm mr-2 outline-none" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);color:#c5d3e4">
                <option value="">Any City</option>
                <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
              </select>
              <button @click="doSearch" class="btn-primary px-6 rounded-xl text-sm font-bold whitespace-nowrap">Search</button>
            </div>
            <div class="flex flex-wrap justify-center gap-2">
              <RouterLink v-for="q in quickLinks" :key="q.label" :to="q.to"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#c5d3e4">
                {{ q.label }}
              </RouterLink>
            </div>
          </div>
        </div>

        <!-- Stats bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
          <div v-for="s in stats" :key="s.label" class="text-center p-4 rounded-2xl" style="background:rgba(13,17,23,0.7);border:1px solid rgba(255,255,255,0.07)">
            <p class="text-2xl font-black mb-1" :style="{color:s.color}">{{ s.value }}</p>
            <p class="text-xs" style="color:#6e7f96">{{ s.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TOP 10 SPONSORED LISTINGS (Advertisements) -->
    <section v-if="ads.length > 0" class="px-4 pb-16 max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
          </div>
          <div>
            <h2 class="text-xl font-black" style="color:#f0f6fc">Top 10 <span style="color:#fbbf24">Sponsored</span></h2>
            <p class="text-xs" style="color:#6e7f96">Premium promoted listings</p>
          </div>
        </div>
        <RouterLink to="/search?sponsored=1" class="text-xs font-semibold" style="color:#6fffd2">View all →</RouterLink>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <div v-for="(ad, i) in ads.slice(0,10)" :key="ad.id"
          class="rounded-2xl overflow-hidden cursor-pointer transition-all hover:scale-[1.02]"
          style="background:#0d1117;border:1px solid rgba(251,191,36,0.2)"
          @click="trackAdClick(ad)">
          <div class="relative aspect-[4/3] overflow-hidden" style="background:rgba(255,255,255,0.03)">
            <img v-if="ad.listing?.thumbnail" :src="ad.listing.thumbnail" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="#2a3a4a" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </div>
            <div class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-xs font-bold" style="background:#fbbf24;color:#05070b">#{{ i+1 }}</div>
          </div>
          <div class="p-3">
            <p class="text-xs font-semibold truncate" style="color:#f0f6fc">{{ ad.listing?.title || ad.name }}</p>
            <p class="text-sm font-black mt-1" style="color:#fbbf24">{{ ad.listing?.price ? fmt(ad.listing.price, ad.listing.currency) : 'Sponsored' }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- AI BEST PRICES — VEHICLES -->
    <section class="px-4 pb-20 max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(111,255,210,0.1);border:1px solid rgba(111,255,210,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
          </div>
          <div>
            <h2 class="text-xl font-black" style="color:#f0f6fc">Best Price <span class="gradient-text">Cars</span></h2>
            <p class="text-xs" style="color:#6e7f96">AI-verified best value vehicles</p>
          </div>
        </div>
        <RouterLink to="/cars" class="text-xs font-semibold" style="color:#6fffd2">View all →</RouterLink>
      </div>
      <div v-if="loadingVehicles" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-64 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else-if="bestVehicles.length===0" class="text-center py-16 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <p style="color:#6e7f96">No vehicle listings available yet.</p>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <ListingCard v-for="l in bestVehicles" :key="l.id" :listing="l" />
      </div>
    </section>

    <!-- NEWEST VEHICLES -->
    <section class="px-4 pb-20 max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(86,216,255,0.1);border:1px solid rgba(86,216,255,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#56d8ff" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <h2 class="text-xl font-black" style="color:#f0f6fc">Newest <span style="color:#56d8ff">Vehicles</span></h2>
            <p class="text-xs" style="color:#6e7f96">Latest car listings</p>
          </div>
        </div>
        <RouterLink to="/cars?sort=newest" class="text-xs font-semibold" style="color:#6fffd2">View all →</RouterLink>
      </div>
      <div v-if="loadingVehicles" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-64 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <ListingCard v-for="l in newestVehicles" :key="l.id" :listing="l" />
      </div>
    </section>

    <!-- AI BEST PRICES — PROPERTIES -->
    <section class="px-4 pb-20 max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          </div>
          <div>
            <h2 class="text-xl font-black" style="color:#f0f6fc">Best Price <span style="color:#a78bfa">Properties</span></h2>
            <p class="text-xs" style="color:#6e7f96">AI-verified best value real estate</p>
          </div>
        </div>
        <RouterLink to="/properties" class="text-xs font-semibold" style="color:#6fffd2">View all →</RouterLink>
      </div>
      <div v-if="loadingProperties" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-64 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else-if="bestProperties.length===0" class="text-center py-16 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <p style="color:#6e7f96">No property listings available yet.</p>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <ListingCard v-for="l in bestProperties" :key="l.id" :listing="l" />
      </div>
    </section>

    <!-- NEWEST PROPERTIES -->
    <section class="px-4 pb-20 max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(86,216,255,0.1);border:1px solid rgba(86,216,255,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#56d8ff" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <h2 class="text-xl font-black" style="color:#f0f6fc">Newest <span style="color:#56d8ff">Properties</span></h2>
            <p class="text-xs" style="color:#6e7f96">Latest real estate listings</p>
          </div>
        </div>
        <RouterLink to="/properties?sort=newest" class="text-xs font-semibold" style="color:#6fffd2">View all →</RouterLink>
      </div>
      <div v-if="loadingProperties" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-64 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <ListingCard v-for="l in newestProperties" :key="l.id" :listing="l" />
      </div>
    </section>

    <!-- SUBSCRIPTION PLANS -->
    <section class="px-4 pb-24 max-w-7xl mx-auto">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-black mb-3" style="color:#f0f6fc">Start Selling <span class="gradient-text">Today</span></h2>
        <p class="text-base max-w-lg mx-auto" style="color:#6e7f96">Choose a plan to list your car or property. AI price analysis included on all paid plans.</p>
      </div>
      <div v-if="loadingPlans" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-64 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="(plan, i) in plans" :key="plan.id" class="rounded-2xl p-6 flex flex-col" :style="i===1?'background:#0d1117;border:2px solid rgba(111,255,210,0.4)':'background:#0d1117;border:1px solid rgba(255,255,255,0.07)'">
          <div v-if="i===1" class="text-center mb-3">
            <span class="text-xs font-bold px-3 py-1 rounded-full" style="background:rgba(111,255,210,0.1);color:#6fffd2;border:1px solid rgba(111,255,210,0.3)">Most Popular</span>
          </div>
          <h3 class="font-black text-lg mb-1" style="color:#f0f6fc">{{ plan.name }}</h3>
          <p class="text-xs mb-4" style="color:#6e7f96">{{ plan.description || 'Perfect for individual sellers' }}</p>
          <div class="mb-4">
            <span class="text-3xl font-black" :style="{color: i===1?'#6fffd2':'#f0f6fc'}">£{{ plan.price_monthly }}</span>
            <span class="text-sm" style="color:#6e7f96">/month</span>
          </div>
          <ul class="space-y-2 text-xs mb-6 flex-1" style="color:#c5d3e4">
            <li class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              {{ plan.max_listings }} listings
            </li>
            <li v-if="plan.ai_price_analysis" class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              AI price analysis
            </li>
            <li v-if="plan.featured_listing" class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              Featured placement
            </li>
            <li v-if="plan.priority_support" class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              Priority support
            </li>
          </ul>
          <RouterLink :to="auth.isAuthenticated ? '/dashboard' : '/register'" class="block text-center py-3 rounded-xl font-bold text-sm" :style="i===1?'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b':'background:rgba(255,255,255,0.06);color:#c5d3e4;border:1px solid rgba(255,255,255,0.1)'">
            {{ plan.price_monthly === 0 ? 'Get Started Free' : 'Choose ' + plan.name }}
          </RouterLink>
        </div>
      </div>
    </section>
  </MainLayout>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import ListingCard from '@/components/listings/ListingCard.vue'
import { useAuthStore } from '@/stores/auth'
import client from '@/api/client'

const router = useRouter()
const auth = useAuthStore()

const searchQuery = ref('')
const searchType = ref('')
const searchCity = ref('')
const cities = ['Girne', 'Lefkosa', 'Gazimağusa', 'Iskele', 'Guzelyurt', 'Lefke']

const bestVehicles = ref<any[]>([])
const newestVehicles = ref<any[]>([])
const bestProperties = ref<any[]>([])
const newestProperties = ref<any[]>([])
const plans = ref<any[]>([])
const ads = ref<any[]>([])
const loadingVehicles = ref(true)
const loadingProperties = ref(true)
const loadingPlans = ref(true)

const stats = ref([
  { label: 'Active Listings', value: '...', color: '#6fffd2' },
  { label: 'Verified Cars', value: '...', color: '#56d8ff' },
  { label: 'Properties', value: '...', color: '#a78bfa' },
  { label: 'AI Analyses', value: '...', color: '#fbbf24' },
])

const quickLinks = [
  { label: 'BMW', to: '/cars?brand=BMW' },
  { label: 'Toyota', to: '/cars?brand=Toyota' },
  { label: 'Apartments', to: '/properties?type=Apartment' },
  { label: 'Villas', to: '/properties?type=Villa' },
  { label: 'Girne', to: '/search?city=Girne' },
]

onMounted(async () => {
  const [statsRes, vehiclesRes, propsRes, plansRes, adsRes] = await Promise.allSettled([
    client.get('/market/stats').catch(() => null),
    client.get('/listings?type=vehicle&per_page=8&sort=price_asc'),
    client.get('/listings?type=property&per_page=8&sort=price_asc'),
    client.get('/subscription-plans'),
    client.get('/advertisements?slot=top10'),
  ])

  if (statsRes.status === 'fulfilled' && statsRes.value) {
    const s = statsRes.value.data
    stats.value = [
      { label: 'Active Listings', value: (s.active_listings || 0).toLocaleString(), color: '#6fffd2' },
      { label: 'Verified Cars', value: (s.vehicle_listings || 0).toLocaleString(), color: '#56d8ff' },
      { label: 'Properties', value: (s.property_listings || 0).toLocaleString(), color: '#a78bfa' },
      { label: 'AI Analyses', value: (s.ai_analyses_today || 0).toLocaleString(), color: '#fbbf24' },
    ]
  }

  if (vehiclesRes.status === 'fulfilled') {
    const data = vehiclesRes.value.data?.data || vehiclesRes.value.data || []
    bestVehicles.value = data.slice(0, 4)
    newestVehicles.value = data.slice(4, 8)
  }
  loadingVehicles.value = false

  if (propsRes.status === 'fulfilled') {
    const data = propsRes.value.data?.data || propsRes.value.data || []
    bestProperties.value = data.slice(0, 4)
    newestProperties.value = data.slice(4, 8)
  }
  loadingProperties.value = false

  if (plansRes.status === 'fulfilled') {
    plans.value = plansRes.value.data || []
  }
  loadingPlans.value = false

  if (adsRes.status === 'fulfilled') {
    ads.value = adsRes.value.data || []
  }
})

function doSearch() {
  const params = new URLSearchParams()
  if (searchQuery.value) params.set('q', searchQuery.value)
  if (searchType.value) params.set('type', searchType.value)
  if (searchCity.value) params.set('city', searchCity.value)
  router.push('/search?' + params.toString())
}

function fmt(v: number, cur = 'GBP') {
  return new Intl.NumberFormat('en-GB', { style: 'currency', currency: cur || 'GBP', maximumFractionDigits: 0 }).format(v)
}

async function trackAdClick(ad: any) {
  await client.post('/advertisements/' + ad.id + '/click').catch(() => {})
  if (ad.listing?.slug) router.push('/listings/' + ad.listing.slug)
  else if (ad.link) window.open(ad.link, '_blank')
}
</script>