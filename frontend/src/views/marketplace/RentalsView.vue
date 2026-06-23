<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="mb-8">
        <h1 class="text-4xl font-black text-white mb-2">Rentals</h1>
        <p class="text-slate-400">Find verified rentals at fair prices</p>
      </div>
      <ListingsGrid type="rental" />
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue'
import ListingsGrid from '@/components/listings/ListingsGrid.vue'
</script>