<template>
  <div class="p-8">
    <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Messages <span class="gradient-text">Inbox</span></h1>
    <p class="text-sm mb-8" style="color:#6e7f96">Inquiries from potential buyers</p>
    <div v-if="loading" class="space-y-3"><div v-for="i in 4" :key="i" class="h-20 rounded-2xl animate-pulse" style="background:#0d1117"></div></div>
    <div v-else-if="msgs.length===0" class="text-center py-20 rounded-2xl" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <p class="text-4xl mb-3">💬</p>
      <p class="font-semibold" style="color:#f0f6fc">No messages yet</p>
      <p class="text-sm mt-1" style="color:#6e7f96">Inquiries from your listings will appear here</p>
    </div>
    <div v-else class="space-y-3">
      <div v-for="m in msgs" :key="m.id" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="flex items-start justify-between gap-4 mb-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm" style="background:rgba(111,255,210,0.1);color:#6fffd2">{{ m.sender?.name?.charAt(0)||'?' }}</div>
            <div>
              <p class="font-semibold text-sm" style="color:#f0f6fc">{{ m.sender?.name }}</p>
              <p class="text-xs" style="color:#6e7f96">Re: {{ m.listing?.title }}</p>
            </div>
          </div>
          <span class="text-xs" style="color:#6e7f96">{{ fmtDate(m.created_at) }}</span>
        </div>
        <p class="text-sm pl-12" style="color:#c5d3e4">{{ m.message }}</p>
        <div class="pl-12 mt-3">
          <a v-if="m.sender?.email" :href="'mailto:'+m.sender.email" class="px-3 py-1.5 text-xs rounded-lg" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">Reply via Email</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import client from '@/api/client'
const msgs = ref<any[]>([]); const loading = ref(true)
onMounted(async () => { try { const r = await client.get('/messages'); msgs.value = r.data.data || r.data || [] } catch {} finally { loading.value = false } })
function fmtDate(d:string) { return d ? new Date(d).toLocaleDateString('en-GB',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'}) : '' }
</script>
