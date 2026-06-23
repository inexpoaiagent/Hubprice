<template>
  <div class="p-8 max-w-2xl">
    <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">KYC <span class="gradient-text">Verification</span></h1>
    <p class="text-sm mb-8" style="color:#6e7f96">Verify your identity to unlock full platform features</p>
    <div v-if="auth.user?.kyc_verified" class="rounded-2xl p-6" style="background:rgba(111,255,210,0.06);border:1px solid rgba(111,255,210,0.2)">
      <div class="flex items-center gap-3">
        <span class="text-3xl">✅</span>
        <div><p class="font-bold" style="color:#6fffd2">Identity Verified</p><p class="text-sm" style="color:#c5d3e4">Your KYC verification is complete. You have full access.</p></div>
      </div>
    </div>
    <div v-else class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <div class="flex items-center gap-3 mb-6 p-4 rounded-xl" style="background:rgba(251,191,36,0.06);border:1px solid rgba(251,191,36,0.2)">
        <span class="text-2xl">⏳</span>
        <div><p class="font-semibold text-sm" style="color:#fbbf24">Verification Pending</p><p class="text-xs" style="color:#c5d3e4">Submit your documents for review</p></div>
      </div>
      <form @submit.prevent="submit" class="space-y-5">
        <div><label class="block text-xs font-medium mb-2" style="color:#6e7f96">Document Type</label>
          <select v-model="form.document_type" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
            <option value="passport">Passport</option><option value="national_id">National ID</option><option value="driving_license">Driving License</option>
          </select>
        </div>
        <div><label class="block text-xs font-medium mb-2" style="color:#6e7f96">Document Number</label><input v-model="form.document_number" placeholder="e.g. A12345678" class="w-full px-4 py-3 rounded-xl text-sm outline-none" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" /></div>
        <div class="p-4 rounded-xl text-center text-sm" style="background:rgba(255,255,255,0.03);border:2px dashed rgba(255,255,255,0.1);color:#6e7f96">📎 Document upload (front + back) — coming soon</div>
        <div v-if="msg" class="text-xs py-2 px-3 rounded-lg" :style="msg.ok?'background:rgba(111,255,210,0.08);color:#6fffd2':'background:rgba(239,68,68,0.08);color:#ef4444'">{{ msg.text }}</div>
        <button type="submit" :disabled="submitting" class="w-full py-3 font-semibold text-sm rounded-xl disabled:opacity-50" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ submitting?'Submitting...':'Submit for Review' }}</button>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import client from '@/api/client'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const submitting = ref(false); const msg = ref<{ok:boolean;text:string}|null>(null)
const form = ref({ document_type: 'passport', document_number: '' })
async function submit() { submitting.value=true; msg.value=null; try { await client.post('/kyc', form.value); await auth.fetchMe(); msg.value={ok:true,text:'Documents submitted for review!'} } catch { msg.value={ok:false,text:'Submission failed.'} } finally { submitting.value=false } }
</script>
