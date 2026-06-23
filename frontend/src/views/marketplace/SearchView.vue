<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="mb-8">
        <h1 class="text-4xl font-black mb-2" style="color:#f0f6fc">
          <span v-if="searchType === 'vehicle'">Cars</span>
          <span v-else-if="searchType === 'property'">Properties</span>
          <span v-else>Search Results</span>
        </h1>
        <p v-if="searchQuery" style="color:#6e7f96;font-size:14px">Results for: "<span style="color:#6fffd2">{{ searchQuery }}</span>"
          <span v-if="searchCity"> · <span style="color:#a78bfa">{{ searchCity }}</span></span>
        </p>
      </div>
      <ListingsGrid
        :initial-search="searchQuery"
        :initial-type="searchType"
        :initial-city="searchCity"
        :key="routeKey"
      />
    </div>
  </MainLayout>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import ListingsGrid from '@/components/listings/ListingsGrid.vue'

const route = useRoute()
const searchQuery = computed(() => (route.query.q as string) || '')
const searchType  = computed(() => (route.query.type as string) || '')
const searchCity  = computed(() => (route.query.city as string) || '')
const routeKey    = computed(() => route.fullPath)
</script>
