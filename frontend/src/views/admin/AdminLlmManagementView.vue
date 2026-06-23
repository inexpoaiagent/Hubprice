<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">LLM <span class="gradient-text">Management</span></h1>
      <p class="text-sm" style="color:#6e7f96">Configure AI models, monitor usage, and manage API keys</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="stat in stats" :key="stat.label" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="'background:rgba('+stat.rgb+',0.1)'">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" :stroke="'rgb('+stat.rgb+')'" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" :d="stat.icon"/>
            </svg>
          </div>
          <span class="text-xs px-2 py-0.5 rounded-full" :style="'background:rgba('+stat.rgb+',0.08);color:rgb('+stat.rgb+')'">{{ stat.badge }}</span>
        </div>
        <p class="text-2xl font-black mb-0.5" :style="'color:rgb('+stat.rgb+')'">{{ stat.value }}</p>
        <p class="text-xs" style="color:#6e7f96">{{ stat.label }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Model Config -->
      <div class="lg:col-span-2 space-y-5">

        <!-- Active Model -->
        <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <h2 class="font-bold mb-5 flex items-center gap-2" style="color:#f0f6fc">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6fffd2" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Active Model
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            <div v-for="model in models" :key="model.id"
              @click="selectedModel = model.id"
              class="p-4 rounded-xl cursor-pointer transition-all"
              :style="selectedModel === model.id
                ? 'background:rgba(111,255,210,0.08);border:1px solid rgba(111,255,210,0.3)'
                : 'background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07)'">
              <div class="flex items-start justify-between mb-2">
                <div>
                  <p class="text-sm font-bold" style="color:#f0f6fc">{{ model.name }}</p>
                  <p class="text-xs" style="color:#6e7f96">{{ model.provider }}</p>
                </div>
                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                  :style="selectedModel === model.id ? 'border-color:#6fffd2' : 'border-color:rgba(255,255,255,0.2)'">
                  <div v-if="selectedModel === model.id" class="w-2 h-2 rounded-full" style="background:#6fffd2"></div>
                </div>
              </div>
              <div class="flex flex-wrap gap-1.5 mb-2">
                <span v-for="tag in model.tags" :key="tag" class="text-xs px-1.5 py-0.5 rounded" style="background:rgba(255,255,255,0.05);color:#8fa6bc">{{ tag }}</span>
              </div>
              <div class="flex justify-between text-xs" style="color:#6e7f96">
                <span>${{ model.inputCost }}/1M in</span>
                <span>${{ model.outputCost }}/1M out</span>
              </div>
            </div>
          </div>

          <button @click="saveModel" :disabled="saving"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
            style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">
            {{ saving ? 'Saving…' : 'Apply Model' }}
          </button>

          <p v-if="saveMsg" class="mt-3 text-xs" :style="saveMsg.ok ? 'color:#6fffd2' : 'color:#ef4444'">{{ saveMsg.text }}</p>
        </div>

        <!-- API Key -->
        <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <h2 class="font-bold mb-4 flex items-center gap-2" style="color:#f0f6fc">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            API Key Status
          </h2>
          <div class="flex items-center gap-3 p-3 rounded-xl mb-4" style="background:rgba(111,255,210,0.05);border:1px solid rgba(111,255,210,0.15)">
            <div class="w-2 h-2 rounded-full" style="background:#6fffd2"></div>
            <span class="text-sm font-medium" style="color:#6fffd2">OpenAI API Key Configured</span>
            <span class="ml-auto font-mono text-xs" style="color:#6e7f96">sk-proj-...VX09AA</span>
          </div>
          <p class="text-xs" style="color:#6e7f96">Key is stored in server <code style="color:#56d8ff">.env</code> and never exposed to the frontend. To rotate, update <code style="color:#56d8ff">OPENAI_API_KEY</code> in your server environment.</p>
        </div>

        <!-- Test Prompt -->
        <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <h2 class="font-bold mb-4 flex items-center gap-2" style="color:#f0f6fc">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Test Connection
          </h2>
          <div class="flex gap-3 mb-4">
            <input v-model="testPrompt" type="text" placeholder="Type a test message…"
              class="flex-1 px-4 py-2.5 rounded-xl text-sm outline-none"
              style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc"
              @keydown.enter="runTest" />
            <button @click="runTest" :disabled="testing"
              class="px-4 py-2.5 rounded-xl text-sm font-bold shrink-0 transition-all"
              style="background:rgba(167,139,250,0.12);color:#a78bfa;border:1px solid rgba(167,139,250,0.25)">
              {{ testing ? '…' : 'Test' }}
            </button>
          </div>
          <div v-if="testResult" class="p-4 rounded-xl text-sm leading-relaxed" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);color:#c5d3e4;white-space:pre-wrap">{{ testResult }}</div>
        </div>
      </div>

      <!-- Right: Usage & Log -->
      <div class="space-y-5">

        <!-- Usage Stats -->
        <div class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <h3 class="font-bold mb-4 text-sm" style="color:#f0f6fc">This Month Usage</h3>
          <div class="space-y-3">
            <div v-for="u in usageBreakdown" :key="u.label">
              <div class="flex justify-between text-xs mb-1">
                <span style="color:#6e7f96">{{ u.label }}</span>
                <span style="color:#c5d3e4">{{ u.value }}</span>
              </div>
              <div style="height:4px;border-radius:2px;background:rgba(255,255,255,0.06)">
                <div style="height:4px;border-radius:2px;transition:width .5s" :style="'width:'+u.pct+'%;background:'+u.color"></div>
              </div>
            </div>
          </div>
          <div class="mt-4 pt-4 flex justify-between items-center" style="border-top:1px solid rgba(255,255,255,0.05)">
            <span class="text-xs" style="color:#6e7f96">Est. Cost</span>
            <span class="text-lg font-black" style="color:#6fffd2">$0.84</span>
          </div>
        </div>

        <!-- Analysis Queue -->
        <div class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm" style="color:#f0f6fc">Analysis Log</h3>
            <button @click="runBatchAnalysis" :disabled="batching"
              class="text-xs px-3 py-1.5 rounded-lg font-bold transition"
              style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">
              {{ batching ? 'Running…' : 'Run All Pending' }}
            </button>
          </div>
          <div class="space-y-2">
            <div v-for="log in analysisLog" :key="log.id" class="flex items-center gap-2.5 py-2" style="border-bottom:1px solid rgba(255,255,255,0.04)">
              <div class="w-1.5 h-1.5 rounded-full shrink-0" :style="'background:'+log.color"></div>
              <div class="flex-1 min-w-0">
                <p class="text-xs truncate" style="color:#c5d3e4">{{ log.title }}</p>
                <p class="text-xs" style="color:#6e7f96">{{ log.time }}</p>
              </div>
              <span class="text-xs font-bold shrink-0" :style="'color:'+log.color">{{ log.status }}</span>
            </div>
          </div>
        </div>

        <!-- Prompt Template -->
        <div class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <h3 class="font-bold text-sm mb-3" style="color:#f0f6fc">Prompt Settings</h3>
          <div class="space-y-3">
            <div>
              <label class="text-xs mb-1.5 block" style="color:#6e7f96">Temperature</label>
              <div class="flex items-center gap-3">
                <input type="range" min="0" max="1" step="0.1" v-model="temperature" class="flex-1" />
                <span class="text-xs font-bold w-6 text-right" style="color:#56d8ff">{{ temperature }}</span>
              </div>
            </div>
            <div>
              <label class="text-xs mb-1.5 block" style="color:#6e7f96">Max Tokens</label>
              <select v-model="maxTokens" class="w-full px-3 py-2 rounded-lg text-sm"
                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:#c5d3e4">
                <option value="200">200 (fast)</option>
                <option value="400">400 (balanced)</option>
                <option value="800">800 (detailed)</option>
              </select>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs" style="color:#6e7f96">Auto-analyze on publish</span>
              <button @click="autoAnalyze = !autoAnalyze"
                class="w-9 h-5 rounded-full transition-colors relative"
                :style="autoAnalyze ? 'background:#6fffd2' : 'background:rgba(255,255,255,0.1)'">
                <div class="absolute top-0.5 w-4 h-4 rounded-full transition-all" style="background:white"
                  :style="autoAnalyze ? 'left:calc(100% - 18px)' : 'left:2px'"></div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import client from '@/api/client'

const selectedModel = ref('gpt-4o-mini')
const saving = ref(false)
const saveMsg = ref<{ ok: boolean; text: string } | null>(null)
const testPrompt = ref('')
const testResult = ref('')
const testing = ref(false)
const batching = ref(false)
const temperature = ref(0.3)
const maxTokens = ref('200')
const autoAnalyze = ref(true)

const models = [
  { id: 'gpt-4o-mini', name: 'GPT-4o Mini', provider: 'OpenAI', tags: ['Fast', 'Cheap', 'Recommended'], inputCost: '0.15', outputCost: '0.60' },
  { id: 'gpt-4o', name: 'GPT-4o', provider: 'OpenAI', tags: ['Powerful', 'Accurate'], inputCost: '2.50', outputCost: '10.00' },
  { id: 'gpt-3.5-turbo', name: 'GPT-3.5 Turbo', provider: 'OpenAI', tags: ['Legacy', 'Budget'], inputCost: '0.50', outputCost: '1.50' },
  { id: 'gpt-4-turbo', name: 'GPT-4 Turbo', provider: 'OpenAI', tags: ['Balanced', 'Premium'], inputCost: '10.00', outputCost: '30.00' },
]

const stats = [
  { label: 'Analyses Today', value: '47', badge: 'Live', rgb: '111,255,210', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { label: 'Total This Month', value: '1,284', badge: 'Jun', rgb: '86,216,255', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { label: 'Tokens Used', value: '2.4M', badge: 'GPT-4o-mini', rgb: '167,139,250', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
  { label: 'Avg Response', value: '1.2s', badge: 'p95', rgb: '251,191,36', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
]

const usageBreakdown = [
  { label: 'Price Analysis', value: '1,048 calls', pct: 82, color: '#6fffd2' },
  { label: 'Batch Re-score', value: '203 calls', pct: 16, color: '#56d8ff' },
  { label: 'Test Calls', value: '33 calls', pct: 3, color: '#a78bfa' },
]

const analysisLog = ref([
  { id: 1, title: '2023 BMW 5-Series • Girne', time: '2 min ago', status: 'Done', color: '#6fffd2' },
  { id: 2, title: 'Sea View Apt • Iskele', time: '8 min ago', status: 'Done', color: '#6fffd2' },
  { id: 3, title: '2021 Toyota Corolla • Lefkosa', time: '15 min ago', status: 'Done', color: '#6fffd2' },
  { id: 4, title: 'Villa 4BR • Kyrenia Hills', time: '31 min ago', status: 'Done', color: '#6fffd2' },
  { id: 5, title: '2019 Mercedes C-Class', time: '1 hr ago', status: 'Pending', color: '#fbbf24' },
])

async function saveModel() {
  saving.value = true
  saveMsg.value = null
  // In production this would call an API endpoint to update the env model
  await new Promise(r => setTimeout(r, 800))
  saveMsg.value = { ok: true, text: `Model set to ${selectedModel.value}. Restart server to apply.` }
  saving.value = false
}

async function runTest() {
  if (!testPrompt.value.trim()) return
  testing.value = true
  testResult.value = ''
  try {
    const { data } = await client.post('/admin/llm/test', {
      prompt: testPrompt.value,
      model: selectedModel.value,
    })
    testResult.value = data.response ?? 'No response'
  } catch {
    testResult.value = '⚠ Test failed — check API key and backend logs.'
  } finally {
    testing.value = false
  }
}

async function runBatchAnalysis() {
  batching.value = true
  try {
    await client.post('/admin/listings/batch-analyze')
    analysisLog.value.forEach(l => { if (l.status === 'Pending') l.status = 'Done'; l.color = '#6fffd2' })
  } catch {
    // silent
  } finally {
    batching.value = false
  }
}
</script>
