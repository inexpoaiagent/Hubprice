<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto px-4 py-24">
      <h1 class="text-4xl font-black text-white mb-4">Contact Us</h1>
      <p class="text-slate-400 mb-8">Reach out to our team for support, partnerships, or press inquiries.</p>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 space-y-4">
        <div><label class="block text-sm text-slate-400 mb-1">Name</label><input class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 outline-none" /></div>
        <div><label class="block text-sm text-slate-400 mb-1">Email</label><input type="email" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 outline-none" /></div>
        <div><label class="block text-sm text-slate-400 mb-1">Message</label><textarea rows="5" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 outline-none resize-none"></textarea></div>
        <button class="w-full py-3 bg-gradient-to-r from-sky-500 to-violet-600 text-white font-bold rounded-xl hover:opacity-90">Send Message</button>
      </div>
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue'
</script>