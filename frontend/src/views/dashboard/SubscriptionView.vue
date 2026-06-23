<template>
  <div class="p-8">
    <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">My <span class="gradient-text">Subscription</span></h1>
    <p class="text-sm mb-8" style="color:#6e7f96">Manage your plan and billing</p>
    <div class="rounded-2xl p-6 mb-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.15)">
      <div class="flex items-center justify-between mb-4">
        <div>
          <p class="text-xs font-medium mb-1" style="color:#6e7f96">Current Plan</p>
          <p class="text-2xl font-black" style="color:#6fffd2">{{ sub?.plan?.name || 'Free' }}</p>
        </div>
        <span class="text-xs px-3 py-1.5 rounded-full font-medium" :style="sub?.status==='active'?'background:rgba(111,255,210,0.1);color:#6fffd2':'background:rgba(255,255,255,0.05);color:#6e7f96'">{{ sub?.status || 'No plan' }}</span>
      </div>
      <div v-if="sub" class="grid grid-cols-2 gap-4 mt-4">
        <div class="p-3 rounded-xl" style="background:rgba(255,255,255,0.03)"><p class="text-xs mb-1" style="color:#6e7f96">Renews</p><p class="text-sm font-semibold" style="color:#f0f6fc">{{ fmtDate(sub.ends_at) }}</p></div>
        <div class="p-3 rounded-xl" style="background:rgba(255,255,255,0.03)"><p class="text-xs mb-1" style="color:#6e7f96">Price</p><p class="text-sm font-semibold" style="color:#f0f6fc">GBP {{ sub.plan?.price_monthly }}/mo</p></div>
      </div>
    </div>
    <RouterLink to="/pricing" class="flex items-center justify-between p-4 rounded-2xl font-semibold" style="background:linear-gradient(135deg,rgba(111,255,210,0.08),rgba(86,216,255,0.08));border:1px solid rgba(111,255,210,0.2);color:#6fffd2"><span>Upgrade Plan</span><span>→</span></RouterLink>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import client from '@/api/client'
const sub = ref<any>(null)
onMounted(async () => { try { const r = await client.get('/my-subscription'); sub.value = r.data } catch {} })
function fmtDate(d:string) { return d ? new Date(d).toLocaleDateString('en-GB',{day:'numeric',month:'long',year:'numeric'}) : 'Never' }
</script>
