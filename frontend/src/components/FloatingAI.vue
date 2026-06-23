<template>
  <!-- Floating AI Button -->
  <div class="floating-ai-wrap" v-if="!hidden">
    <!-- Chat panel -->
    <Transition name="chat-slide">
      <div v-if="chatOpen" class="chat-panel">
        <div class="chat-header">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b;font-weight:900">AI</div>
            <div>
              <p class="text-xs font-bold" style="color:#f0f6fc">HubPrice AI</p>
              <p class="text-xs" style="color:#6fffd2">● Online</p>
            </div>
          </div>
          <button @click="chatOpen = false" style="color:#6e7f96;font-size:20px;line-height:1">×</button>
        </div>
        <div class="chat-messages" ref="messagesEl">
          <div v-for="msg in messages" :key="msg.id" :class="['chat-msg', msg.role === 'user' ? 'chat-msg--user' : 'chat-msg--ai']">
            <div class="chat-bubble" :class="msg.role === 'user' ? 'bubble-user' : 'bubble-ai'">
              {{ msg.text }}
            </div>
          </div>
          <div v-if="thinking" class="chat-msg chat-msg--ai">
            <div class="chat-bubble bubble-ai" style="display:flex;gap:4px;align-items:center">
              <span class="dot-pulse"></span><span class="dot-pulse" style="animation-delay:.2s"></span><span class="dot-pulse" style="animation-delay:.4s"></span>
            </div>
          </div>
        </div>
        <div class="chat-input-row">
          <input v-model="inputText" @keydown.enter="sendMessage" type="text" placeholder="Ask about prices, market trends..." class="chat-input" />
          <button @click="sendMessage" class="chat-send" :disabled="!inputText.trim() || thinking">→</button>
        </div>
      </div>
    </Transition>

    <!-- Trigger button -->
    <button @click="chatOpen = !chatOpen" class="ai-fab" :class="{ 'ai-fab--active': chatOpen }" aria-label="AI Assistant">
      <span v-if="!chatOpen" class="fab-icon">✦</span>
      <span v-else class="fab-icon" style="font-size:18px">×</span>
      <span v-if="!chatOpen" class="fab-label">AI</span>
    </button>
  </div>
</template>
<script setup lang="ts">
import { ref, nextTick, onMounted } from "vue"
const chatOpen = ref(false); const hidden = ref(false); const thinking = ref(false); const inputText = ref(""); const messagesEl = ref<HTMLElement>()
const messages = ref([
  { id: 1, role: "ai", text: "Hello! I am HubPrice AI. I can help you analyze car prices, property values, and market trends in North Cyprus. What would you like to know?" }
])
let nextId = 2

const quickReplies = [
  "What is the average car price in Girne?",
  "Is now a good time to buy property?",
  "Which areas have the best ROI?",
]

async function sendMessage() {
  const text = inputText.value.trim(); if (!text) return
  messages.value.push({ id: nextId++, role: "user", text })
  inputText.value = ""; thinking.value = true
  await scrollBottom()
  await new Promise(r => setTimeout(r, 900 + Math.random() * 600))
  const responses: Record<string, string> = {
    price: "Based on our market data, the average vehicle price in North Cyprus is around 380,000 TRY. Prices vary significantly by brand and condition. Our AI trust score helps you identify fair deals.",
    property: "North Cyprus property prices have increased 18% YoY. Iskele and Kyrenia (Girne) are hottest areas. Average apartment: £85,000-£165,000. Villas: £200,000-£750,000.",
    roi: "Areas with highest rental ROI: Iskele (8-12%), Kyrenia centre (6-9%), Famagusta near university (7-10%). Long Beach development shows strong capital growth.",
    default: "Great question! Based on our AI market analysis of North Cyprus, I can see strong buying opportunities across vehicle and property sectors. Would you like a specific price analysis or market trend report?"
  }
  const lower = text.toLowerCase()
  const reply = lower.includes("price") || lower.includes("car") || lower.includes("vehicle") ? responses.price
    : lower.includes("property") || lower.includes("buy") || lower.includes("invest") ? responses.property
    : lower.includes("roi") || lower.includes("return") || lower.includes("area") ? responses.roi
    : responses.default
  messages.value.push({ id: nextId++, role: "ai", text: reply })
  thinking.value = false
  await scrollBottom()
}
async function scrollBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}
</script>
<style scoped>
.floating-ai-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 100; display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }
.ai-fab { width: 56px; height: 56px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; background: linear-gradient(135deg,#6fffd2,#56d8ff); border: none; cursor: pointer; box-shadow: 0 4px 24px rgba(111,255,210,0.35); transition: transform 0.2s, box-shadow 0.2s; }
.ai-fab:hover { transform: scale(1.08); box-shadow: 0 6px 32px rgba(111,255,210,0.5); }
.ai-fab--active { background: linear-gradient(135deg,#0d1117,#1a2740); border: 1px solid rgba(111,255,210,0.3); box-shadow: 0 4px 24px rgba(111,255,210,0.15); }
.fab-icon { font-size: 20px; color: #05070b; font-weight: 900; line-height: 1; }
.ai-fab--active .fab-icon { color: #6fffd2; }
.fab-label { font-size: 9px; font-weight: 900; color: #05070b; letter-spacing: 0.1em; }
.chat-panel { width: 320px; border-radius: 20px; overflow: hidden; background: #080d13; border: 1px solid rgba(111,255,210,0.2); box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(111,255,210,0.06); display: flex; flex-direction: column; max-height: 460px; }
.chat-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 16px 12px; background: rgba(111,255,210,0.04); border-bottom: 1px solid rgba(111,255,210,0.12); }
.chat-messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px; min-height: 200px; scrollbar-width: thin; scrollbar-color: #1e3a4a transparent; }
.chat-msg { display: flex; }
.chat-msg--user { justify-content: flex-end; }
.chat-bubble { max-width: 84%; padding: 10px 14px; border-radius: 14px; font-size: 13px; line-height: 1.5; }
.bubble-ai { background: rgba(111,255,210,0.07); color: #c5d3e4; border: 1px solid rgba(111,255,210,0.1); border-radius: 14px 14px 14px 4px; }
.bubble-user { background: linear-gradient(135deg,#6fffd2,#56d8ff); color: #05070b; font-weight: 600; border-radius: 14px 14px 4px 14px; }
.chat-input-row { display: flex; gap: 8px; padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.06); }
.chat-input { flex: 1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #f0f6fc; padding: 8px 12px; font-size: 13px; outline: none; }
.chat-input:focus { border-color: rgba(111,255,210,0.4); }
.chat-send { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg,#6fffd2,#56d8ff); color: #05070b; font-size: 16px; font-weight: 900; border: none; cursor: pointer; flex-shrink: 0; }
.chat-send:disabled { opacity: 0.4; cursor: not-allowed; }
.dot-pulse { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #6fffd2; animation: dot-blink 1.2s ease-in-out infinite; }
@keyframes dot-blink { 0%,80%,100% { transform: scale(0.6); opacity: 0.4; } 40% { transform: scale(1); opacity: 1; } }
.chat-slide-enter-active, .chat-slide-leave-active { transition: opacity 0.25s, transform 0.3s cubic-bezier(.4,0,.2,1); }
.chat-slide-enter-from, .chat-slide-leave-to { opacity: 0; transform: translateY(16px) scale(0.95); }
</style>