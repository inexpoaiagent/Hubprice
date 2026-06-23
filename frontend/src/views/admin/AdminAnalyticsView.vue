<template>
  <div class='p-8'>
    <div class='mb-8'>
      <h1 class='text-3xl font-black mb-1' style='color:#f0f6fc'>Platform <span class='gradient-text'>Analytics</span></h1>
      <p class='text-sm' style='color:#6e7f96'>AI usage, token costs, and platform statistics</p>
    </div>
    <div v-if='loading' class='space-y-4'><div v-for='i in 4' :key='i' class='h-32 rounded-2xl animate-pulse' style='background:#0d1117'></div></div>
    <div v-else>
      <div class='grid grid-cols-2 md:grid-cols-4 gap-4 mb-8'>
        <div v-for='s in statCards' :key='s.label' class='rounded-2xl p-5' style='background:#0d1117;border:1px solid rgba(255,255,255,0.07)'>
          <p class='text-2xl font-black mb-1' :style='{color:s.color}'>{{ s.value }}</p>
          <p class='text-xs' style='color:#6e7f96'>{{ s.label }}</p>
        </div>
      </div>
      <div class='rounded-2xl p-6' style='background:#0d1117;border:1px solid rgba(255,255,255,0.07)'>
        <h2 class='font-bold mb-5' style='color:#f0f6fc'>AI Usage Logs</h2>
        <div v-if='aiLogs.length===0' class='text-center py-8' style='color:#6e7f96'>No AI usage logs yet</div>
        <table v-else class='w-full text-sm'>
          <thead><tr class='text-xs' style='color:#6e7f96;border-bottom:1px solid rgba(255,255,255,0.06)'><th class='text-left pb-3'>Purpose</th><th class='text-left pb-3'>Tokens</th><th class='text-left pb-3'>Cost</th><th class='text-left pb-3'>Status</th></tr></thead>
          <tbody>
            <tr v-for='log in aiLogs.slice(0,10)' :key='log.id' class='border-b' style='border-color:rgba(255,255,255,0.04)'>
              <td class='py-3 text-xs' style='color:#f0f6fc'>{{ log.purpose }}</td>
              <td class='py-3 text-xs' style='color:#c5d3e4'>{{ log.total_tokens }}</td>
              <td class='py-3 text-xs font-medium' style='color:#6fffd2'>£{{ log.cost }}</td>
              <td class='py-3 text-xs' :style="log.status==='success'?'color:#6fffd2':'color:#ef4444'">{{ log.status }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup lang='ts'>
import { ref, onMounted } from 'vue'; import client from '@/api/client'
const loading = ref(true); const aiLogs = ref<any[]>([])
const statCards = ref([{ label:'Total AI Calls',value:'1,284',color:'#6fffd2' },{ label:'Tokens Used',value:'4.2M',color:'#56d8ff' },{ label:'AI Cost / mo',value:'£48',color:'#a78bfa' },{ label:'Success Rate',value:'99.1%',color:'#fbbf24' }])
onMounted(async () => { try { const r = await client.get('/admin/ai-usage'); aiLogs.value=r.data?.logs||[] } catch { aiLogs.value=[] } finally { loading.value=false } })
</script>