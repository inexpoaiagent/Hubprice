<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <p class="text-5xl mb-4">📋</p>
      <h2 class="text-xl font-bold mb-2" style="color:#f0f6fc">Listing Not Found</h2>
      <p class="text-sm mb-6" style="color:#6e7f96">This listing may have expired or been removed.</p>
      <RouterLink to="/search" class="btn-primary">Browse All Listings</RouterLink>
    </div>
  </div>
</template>
<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>
