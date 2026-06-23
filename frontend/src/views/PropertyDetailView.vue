<template>
  <div v-if="loading" class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div class="w-12 h-12 rounded-full border-2 animate-spin mx-auto mb-4" style="border-color:rgba(111,255,210,0.2);border-top-color:#6fffd2"></div>
      <p style="color:#6e7f96">Loading property...</p>
    </div>
  </div>

  <div v-else-if="!listing" class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <p class="text-6xl mb-4">🏠</p>
      <h2 class="text-xl font-bold mb-2" style="color:#f0f6fc">Property Not Found</h2>
      <RouterLink to="/properties" class="btn-primary mt-4 inline-block">Browse Properties</RouterLink>
    </div>
  </div>

  <div v-else>
    <!-- Hero gallery -->
    <div class="relative" style="height:500px;background:#080d13">
      <img v-if="activeImage && !imgErr" :src="activeImage" :alt="listing.title" class="w-full h-full object-cover" @error="imgErr = true" />
      <div v-else class="w-full h-full flex items-center justify-center text-9xl opacity-20">🏠</div>
      <div class="absolute inset-0" style="background:linear-gradient(to bottom,rgba(5,7,11,0.15) 0%,transparent 35%,rgba(5,7,11,0.85) 100%)"></div>

      <!-- Thumbnails -->
      <div v-if="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
        <button v-for="(img, i) in images" :key="i" @click="setImg(img)" class="w-16 h-11 rounded-xl overflow-hidden border-2 transition" :style="activeImage === img ? 'border-color:#6fffd2' : 'border-color:rgba(255,255,255,0.2)'">
          <img :src="img" class="w-full h-full object-cover" />
        </button>
      </div>

      <RouterLink to="/properties" class="absolute top-5 left-5 flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium" style="background:rgba(0,0,0,0.5);color:#f0f6fc;backdrop-filter:blur(8px)">
        ← Properties
      </RouterLink>

      <!-- Quick info overlay bottom-left -->
      <div class="absolute bottom-8 left-8">
        <h1 class="text-3xl font-black mb-2 drop-shadow-xl" style="color:#fff">{{ listing.title }}</h1>
        <div class="flex items-center gap-3 text-sm" style="color:rgba(255,255,255,0.8)">
          <span>📍 {{ listing.city }}, North Cyprus</span>
          <span v-if="property?.bedrooms">· {{ property.bedrooms }} bed</span>
          <span v-if="property?.bathrooms">· {{ property.bathrooms }} bath</span>
          <span v-if="property?.area_sqm">· {{ property.area_sqm }}m²</span>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left main -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Key facts ribbon -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="f in keyFacts" :key="f.label" class="rounded-xl p-4 text-center" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
              <div class="text-2xl mb-1">{{ f.icon }}</div>
              <div class="text-base font-black" style="color:#f0f6fc">{{ f.value }}</div>
              <div class="text-xs mt-0.5" style="color:#6e7f96">{{ f.label }}</div>
            </div>
          </div>

          <!-- Property details -->
          <div class="rounded-2xl p-6" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
            <h2 class="font-bold text-lg mb-5 flex items-center gap-2" style="color:#f0f6fc">
              <span style="color:#56d8ff">🏠</span> Property Details
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
              <SpecItem v-if="property?.bedrooms" icon="🛏" label="Bedrooms" :value="String(property.bedrooms)" />
              <SpecItem v-if="property?.bathrooms" icon="🚿" label="Bathrooms" :value="String(property.bathrooms)" />
              <SpecItem v-if="property?.area_sqm" icon="📐" label="Area (m²)" :value="String(property.area_sqm) + ' m²'" />
              <SpecItem v-if="property?.area_sqft" icon="📏" label="Area (ft²)" :value="String(property.area_sqft) + ' ft²'" />
              <SpecItem v-if="property?.furnishing" icon="🛋" label="Furnishing" :value="capitalize(property.furnishing)" />
              <SpecItem v-if="property?.condition" icon="✨" label="Condition" :value="capitalize(property.condition)" />
              <SpecItem v-if="property?.floor_number != null" icon="🏢" label="Floor" :value="property.floor_number === 0 ? 'Ground' : String(property.floor_number)" />
              <SpecItem v-if="property?.build_year" icon="📅" label="Built" :value="String(property.build_year)" />
              <SpecItem icon="📍" label="Location" :value="listing.city" />
            </div>

            <!-- Amenities -->
            <div class="mt-6 pt-5" style="border-top:1px solid rgba(255,255,255,0.06)">
              <h3 class="text-sm font-semibold mb-3" style="color:#c5d3e4">Amenities</h3>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <div v-for="a in amenitiesList" :key="a.label" class="flex items-center gap-2 text-xs py-2 px-3 rounded-lg" :style="a.has ? 'background:rgba(111,255,210,0.06);color:#6fffd2;border:1px solid rgba(111,255,210,0.15)' : 'background:rgba(255,255,255,0.02);color:#6e7f96;border:1px solid rgba(255,255,255,0.05)'">
                  <span>{{ a.has ? '✓' : '✗' }}</span> {{ a.label }}
                </div>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="rounded-2xl p-6" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
            <h2 class="font-bold text-lg mb-4" style="color:#f0f6fc">About This Property</h2>
            <p class="text-sm leading-relaxed whitespace-pre-line" style="color:#c5d3e4">{{ listing.description }}</p>
          </div>

          <!-- Location info -->
          <div class="rounded-2xl p-6" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
            <h2 class="font-bold text-lg mb-4" style="color:#f0f6fc">📍 Location</h2>
            <div class="grid grid-cols-3 gap-4">
              <div v-for="loc in nearbyLocations" :key="loc.label" class="rounded-xl p-3 text-center" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06)">
                <div class="text-lg mb-1">{{ loc.icon }}</div>
                <div class="text-sm font-bold" style="color:#f0f6fc">{{ loc.dist }}</div>
                <div class="text-xs" style="color:#6e7f96">{{ loc.label }}</div>
              </div>
            </div>
          </div>

          <!-- AI Investment Analysis -->
          <div class="rounded-2xl p-6" style="background:linear-gradient(135deg,rgba(86,216,255,0.04),rgba(167,139,250,0.03));border:1px solid rgba(86,216,255,0.15)">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs" style="background:linear-gradient(135deg,#56d8ff,#a78bfa);color:#05070b">AI</div>
              <h2 class="font-bold text-lg" style="color:#f0f6fc">AI Investment Analysis</h2>
            </div>

            <div v-if="listing.market_min_price && listing.market_max_price" class="mb-6">
              <div class="flex justify-between text-xs mb-2" style="color:#6e7f96">
                <span>Min: {{ formatPrice(listing.market_min_price, listing.currency) }}</span>
                <span>Max: {{ formatPrice(listing.market_max_price, listing.currency) }}</span>
              </div>
              <div class="relative h-3 rounded-full" style="background:rgba(255,255,255,0.07)">
                <div class="h-3 rounded-full" style="background:linear-gradient(90deg,rgba(86,216,255,0.3),rgba(167,139,250,0.3));width:100%"></div>
                <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2" style="background:#56d8ff;border-color:#05070b;box-shadow:0 0 12px rgba(86,216,255,0.6)" :style="{ left: pricePosition + '%', transform: 'translateX(-50%) translateY(-50%)' }"></div>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <ScoreGauge v-if="listing.ai_trust_score" label="Trust Score" :value="listing.ai_trust_score" color="#6fffd2" />
              <ScoreGauge v-if="listing.ai_investment_score" label="ROI Score" :value="listing.ai_investment_score" color="#a78bfa" />
              <ScoreGauge v-if="listing.ai_confidence_score" label="Confidence" :value="listing.ai_confidence_score" color="#56d8ff" />
            </div>

            <div class="mt-5 p-4 rounded-xl text-sm leading-relaxed" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#c5d3e4">
              <strong style="color:#56d8ff">AI Analysis:</strong> This property in {{ listing.city }} is priced at <strong style="color:#f0f6fc">{{ formatPrice(listing.price, listing.currency) }}</strong>. Based on comparable listings and market trends, this represents a <strong :style="`color:${aiColor}`">{{ aiStatusLabel }}</strong> opportunity. North Cyprus property market has shown consistent growth, particularly in coastal areas.
            </div>
          </div>

          <!-- Similar properties -->
          <div v-if="comparables.length" class="rounded-2xl p-6" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
            <h2 class="font-bold text-lg mb-5" style="color:#f0f6fc">Similar Properties</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <RouterLink v-for="c in comparables" :key="c.id" :to="`/listings/${c.slug}`" class="flex items-center gap-3 p-3 rounded-xl transition-all" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06)" @mouseenter="(e:any) => e.currentTarget.style.borderColor='rgba(86,216,255,0.2)'" @mouseleave="(e:any) => e.currentTarget.style.borderColor='rgba(255,255,255,0.06)'">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0" style="background:rgba(255,255,255,0.04)">🏠</div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium truncate" style="color:#f0f6fc">{{ c.title }}</p>
                  <p class="text-xs font-bold" style="color:#56d8ff">{{ formatPrice(c.price, c.currency) }}</p>
                </div>
              </RouterLink>
            </div>
          </div>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-5">
          <div class="rounded-2xl p-6 sticky top-20" style="background:#0d1520;border:1px solid rgba(86,216,255,0.2)">
            <p class="text-3xl font-black mb-0.5" style="color:#f0f6fc">{{ formatPrice(listing.price, listing.currency) }}</p>
            <p v-if="property?.area_sqm" class="text-sm mb-4" style="color:#6e7f96">{{ formatPrice(listing.price / property.area_sqm, listing.currency) }}/m²</p>
            <p v-if="listing.price_negotiable" class="text-sm mb-4" style="color:#56d8ff">✓ Price negotiable</p>

            <!-- Battery -->
            <div v-if="listing.price_battery_percent" class="mb-5 p-3 rounded-xl" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06)">
              <div class="flex justify-between text-xs mb-2">
                <span style="color:#6e7f96">Investment Score</span>
                <span :style="`color:${batteryColor};font-weight:700`">{{ listing.price_battery_percent }}%</span>
              </div>
              <div class="h-2 rounded-full" style="background:rgba(255,255,255,0.07)">
                <div class="h-2 rounded-full" :style="`width:${listing.price_battery_percent}%;background:${batteryColor}`"></div>
              </div>
            </div>

            <!-- Seller -->
            <div class="flex items-center gap-3 mb-5 p-3 rounded-xl" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06)">
              <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold" style="background:linear-gradient(135deg,rgba(86,216,255,0.2),rgba(167,139,250,0.2));color:#56d8ff">
                {{ listing.user?.name?.charAt(0) || 'A' }}
              </div>
              <div>
                <p class="text-sm font-semibold" style="color:#f0f6fc">{{ listing.user?.name || 'Agency' }}</p>
                <p class="text-xs" style="color:#6e7f96">Licensed Agent</p>
              </div>
            </div>

            <div class="space-y-3">
              <button @click="showInquiry = !showInquiry" class="w-full py-3.5 rounded-xl font-bold text-sm" style="background:linear-gradient(135deg,#56d8ff,#a78bfa);color:#05070b">
                📩 Request Viewing
              </button>
              <button @click="toggleFavorite" class="w-full py-3.5 rounded-xl font-semibold text-sm" :style="isFavorited ? 'background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.25)' : 'background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.1)'">
                {{ isFavorited ? '❤️ Saved' : '♡ Save Property' }}
              </button>
              <a href="tel:+905001234567" class="block w-full py-3.5 rounded-xl font-semibold text-sm text-center" style="background:rgba(255,255,255,0.04);color:#c5d3e4;border:1px solid rgba(255,255,255,0.1)">
                📞 Call Agent
              </a>
            </div>

            <Transition name="slide-down">
              <div v-if="showInquiry" class="mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,0.06)">
                <textarea v-model="inquiryMsg" rows="3" placeholder="I am interested in viewing this property..." class="w-full rounded-xl p-3 text-sm resize-none outline-none" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc"></textarea>
                <button @click="sendInquiry" :disabled="sendingInquiry" class="w-full mt-2 py-2.5 rounded-xl text-sm font-bold" style="background:linear-gradient(135deg,#56d8ff,#a78bfa);color:#05070b">
                  {{ sendingInquiry ? 'Sending...' : 'Send Request' }}
                </button>
                <p v-if="inquirySent" class="text-xs text-center mt-2" style="color:#6fffd2">✓ Request sent!</p>
              </div>
            </Transition>
          </div>

          <!-- Why buy in NC -->
          <div class="rounded-2xl p-5" style="background:#0d1520;border:1px solid rgba(255,255,255,0.07)">
            <h3 class="text-sm font-bold mb-3" style="color:#f0f6fc">🌟 Why North Cyprus?</h3>
            <ul class="space-y-2">
              <li v-for="r in whyNC" :key="r" class="text-xs flex items-start gap-2" style="color:#6e7f96">
                <span style="color:#56d8ff;flex-shrink:0">✓</span> {{ r }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useListingsStore } from '@/stores/listings'
import type { Listing, Property } from '@/types'

const SpecItem = { props: ['icon', 'label', 'value'], template: `<div class="flex flex-col gap-1"><span class="text-xs" style="color:#6e7f96">{{ icon }} {{ label }}</span><span class="text-sm font-semibold" style="color:#f0f6fc">{{ value }}</span></div>` }
const ScoreGauge = { props: ['label', 'value', 'color'], template: `<div class="text-center p-3 rounded-xl" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06)"><div class="text-2xl font-black mb-1" :style="{color}">{{ value }}</div><div class="text-xs" style="color:#6e7f96">{{ label }}</div><div class="mt-2 h-1 rounded-full" style="background:rgba(255,255,255,0.07)"><div class="h-1 rounded-full" :style="{width: value + '%', background: color}"></div></div></div>` }

const route = useRoute()
const store = useListingsStore()
const loading = ref(true)
const listing = ref<Listing | null>(null)
const comparables = ref<any[]>([])
const isFavorited = ref(false)
const activeImage = ref<string | null>(null)
const imgErr = ref(false)
const showInquiry = ref(false)
const inquiryMsg = ref('')
const sendingInquiry = ref(false)
const inquirySent = ref(false)

const property = computed(() => listing.value?.listable as Property | null)
const images = computed(() => { const l = listing.value as any; return l?.images?.length ? l.images : (l?.thumbnail ? [l.thumbnail] : []) })

const keyFacts = computed(() => {
  const p = property.value
  const l = listing.value
  if (!p || !l) return []
  return [
    { icon: '🛏', label: 'Bedrooms', value: String(p.bedrooms ?? '—') },
    { icon: '🚿', label: 'Bathrooms', value: String(p.bathrooms ?? '—') },
    { icon: '📐', label: 'Size', value: p.area_sqm ? p.area_sqm + ' m²' : '—' },
    { icon: '🛋', label: 'Furnished', value: p.furnishing ? capitalize(p.furnishing) : '—' },
  ]
})

const amenitiesList = computed(() => {
  const p = property.value
  return [
    { label: 'Parking', has: p?.has_parking },
    { label: 'Garden', has: p?.has_garden },
    { label: 'Pool', has: p?.has_pool },
    { label: 'Elevator', has: p?.has_elevator },
    { label: 'Air Con', has: true },
    { label: 'Security', has: true },
  ]
})

const nearbyLocations = computed(() => {
  const nearby = (property.value as any)?.nearby_locations ?? {}
  return [
    { icon: '✈', label: 'Airport', dist: nearby.airport ?? '~30km' },
    { icon: '🏖', label: 'Beach', dist: nearby.beach ?? '~5km' },
    { icon: '🏙', label: 'City Centre', dist: nearby.city_centre ?? '~3km' },
  ]
})

const pricePosition = computed(() => {
  const l = listing.value
  if (!l?.market_min_price || !l?.market_max_price) return 50
  const range = l.market_max_price - l.market_min_price
  if (range <= 0) return 50
  return Math.max(2, Math.min(98, ((l.price - l.market_min_price) / range) * 100))
})

const aiStatusLabel = computed(() => {
  const map: Record<string, string> = { fair: 'strong value', slightly_overpriced: 'slightly above market', overpriced: 'above market' }
  return map[listing.value?.ai_price_status ?? 'fair'] ?? 'fair value'
})
const aiColor = computed(() => listing.value?.ai_price_status === 'fair' ? '#6fffd2' : '#fbbf24')
const batteryColor = computed(() => { const p = listing.value?.price_battery_percent ?? 0; return p >= 85 ? '#6fffd2' : p >= 70 ? '#56d8ff' : '#fbbf24' })

function setImg(url: string) { activeImage.value = url; imgErr.value = false }
function capitalize(s: string) { return s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, ' ') : '' }
function formatPrice(val: number, currency = 'GBP') {
  try { return new Intl.NumberFormat('en-GB', { style: 'currency', currency: currency || 'GBP', maximumFractionDigits: 0 }).format(val) }
  catch { return `${currency} ${Number(val)?.toLocaleString()}` }
}

async function toggleFavorite() { try { await store.toggleFavorite(listing.value!.id); isFavorited.value = !isFavorited.value } catch {} }
async function sendInquiry() {
  if (!inquiryMsg.value.trim()) return
  sendingInquiry.value = true
  try { await store.sendInquiry(listing.value!.id, inquiryMsg.value); inquirySent.value = true; inquiryMsg.value = ''; setTimeout(() => { showInquiry.value = false; inquirySent.value = false }, 3000) }
  catch {} finally { sendingInquiry.value = false }
}

const whyNC = [
  'No capital gains tax or inheritance tax',
  'Strong rental yields of 6-12% per annum',
  'Growing expat community and tourism sector',
  'Affordable prices vs EU/UK property markets',
  'Mediterranean climate year-round',
]

async function load() {
  loading.value = true
  try {
    const res = await store.fetchListing(route.params.slug as string) as any
    listing.value = res.listing ?? res
    comparables.value = res.comparable_listings ?? []
    isFavorited.value = res.is_favorited ?? false
    const imgs = (listing.value as any)?.images
    activeImage.value = imgs?.[0] ?? (listing.value as any)?.thumbnail ?? null
  } catch { listing.value = null } finally { loading.value = false }
}

onMounted(load)
watch(() => route.params.slug, load)
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; overflow: hidden; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; max-height: 0; }
.slide-down-enter-to, .slide-down-leave-from { opacity: 1; max-height: 400px; }
</style>
