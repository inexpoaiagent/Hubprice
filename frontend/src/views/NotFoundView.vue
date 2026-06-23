<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center text-center">
    <div>
      <p class="text-8xl mb-6">🔍</p>
      <h1 class="text-4xl font-black text-white mb-4">Page Not Found</h1>
      <p class="text-slate-400 mb-8">The page you are looking for does not exist.</p>
      <RouterLink to="/" class="px-6 py-3 bg-gradient-to-r from-sky-500 to-violet-600 text-white font-semibold rounded-xl hover:opacity-90 transition">Go Home</RouterLink>
    </div>
  </div>
</template>
<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>