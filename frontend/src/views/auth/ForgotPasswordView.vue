<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-white mb-2">Reset Password</h1>
        <p class="text-slate-400">Enter your email to receive a reset link</p>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8">
        <div v-if="sent" class="text-center text-emerald-400 py-4">Check your email for a reset link.</div>
        <form v-else @submit.prevent="handleSubmit" class="space-y-5">
          <input v-model="email" type="email" required placeholder="Email address" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-sky-500 transition" />
          <button type="submit" :disabled="loading" class="w-full py-3 bg-gradient-to-r from-sky-500 to-violet-600 text-white font-bold rounded-xl hover:opacity-90 transition">
            {{ loading ? 'Sending...' : 'Send Reset Link' }}
          </button>
        </form>
        <p class="mt-4 text-center text-sm"><RouterLink to="/login" class="text-sky-400">Back to login</RouterLink></p>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'; import { RouterLink } from 'vue-router'; import client from '@/api/client'
const email = ref(''); const loading = ref(false); const sent = ref(false)
async function handleSubmit() { loading.value = true; try { await client.post('/auth/forgot-password', { email: email.value }); sent.value = true } finally { loading.value = false } }
</script>