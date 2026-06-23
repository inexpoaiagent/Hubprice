<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Admin <span class="gradient-text">Dashboard</span></h1>
        <p class="text-sm" style="color:#6e7f96">Platform overview — {{ new Date().toLocaleDateString("en-GB", {weekday:"long",year:"numeric",month:"long",day:"numeric"}) }}</p>
      </div>
      <div class="ai-badge">Live</div>
    </div>

    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="i in 8" :key="i" class="rounded-2xl h-28 animate-pulse" style="background:#0d1117"></div>
    </div>

    <template v-else>
      <!-- Stats grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div v-for="s in stats" :key="s.label" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <div class="text-2xl mb-1">{{ s.icon }}</div>
          <div class="text-3xl font-black mb-0.5" :style="{color: s.color}">{{ s.value?.toLocaleString() || 0 }}</div>
          <div class="text-xs" style="color:#6e7f96">{{ s.label }}</div>
        </div>
      </div>

      <!-- Two columns -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Listings -->
        <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold" style="color:#f0f6fc">Recent Listings</h2>
            <RouterLink to="/admin/listings" class="text-xs" style="color:#6fffd2">View all →</RouterLink>
          </div>
          <div class="space-y-3">
            <div v-for="l in data?.recent_listings?.slice(0,6)" :key="l.id" class="flex items-center justify-between p-3 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04)">
              <div class="flex items-center gap-3">
                <span class="text-xl">{{ {vehicle:"🚗",property:"🏠",rental:"🔑"}[l.type] || "📋" }}</span>
                <div>
                  <p class="text-xs font-medium truncate max-w-[160px]" style="color:#f0f6fc">{{ l.title }}</p>
                  <p class="text-xs" style="color:#6e7f96">{{ l.user?.name }}</p>
                </div>
              </div>
              <span class="text-xs px-2 py-0.5 rounded-full capitalize" :class="statusClass(l.status)">{{ l.status }}</span>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold" style="color:#f0f6fc">Recent Users</h2>
            <RouterLink to="/admin/users" class="text-xs" style="color:#6fffd2">View all →</RouterLink>
          </div>
          <div class="space-y-3">
            <div v-for="u in data?.recent_users?.slice(0,6)" :key="u.id" class="flex items-center justify-between p-3 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04)">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background:linear-gradient(135deg,rgba(111,255,210,0.2),rgba(86,216,255,0.2));color:#6fffd2">{{ u.name?.charAt(0) }}</div>
                <div>
                  <p class="text-xs font-medium" style="color:#f0f6fc">{{ u.name }}</p>
                  <p class="text-xs truncate max-w-[120px]" style="color:#6e7f96">{{ u.email }}</p>
                </div>
              </div>
              <span class="text-xs px-2 py-0.5 rounded-full capitalize" style="background:rgba(86,216,255,0.1);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">{{ u.role }}</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from "vue"; import { RouterLink } from "vue-router"; import client from "@/api/client"
const loading = ref(true); const data = ref<any>(null)
onMounted(async () => { const r = await client.get("/admin/dashboard"); data.value = r.data; loading.value = false })
const stats = computed(() => data.value ? [
  { icon: "👥", label: "Total Users", value: data.value.stats.total_users, color: "#56d8ff" },
  { icon: "📋", label: "Active Listings", value: data.value.stats.active_listings, color: "#6fffd2" },
  { icon: "⏳", label: "Pending Review", value: data.value.stats.pending_listings, color: "#fbbf24" },
  { icon: "🚗", label: "Vehicles", value: data.value.stats.vehicle_listings, color: "#6fffd2" },
  { icon: "🏠", label: "Properties", value: data.value.stats.property_listings, color: "#56d8ff" },
  { icon: "🔑", label: "Rentals", value: data.value.stats.rental_listings, color: "#a78bfa" },
  { icon: "🏢", label: "Dealers", value: data.value.stats.dealers, color: "#fbbf24" },
  { icon: "🛡", label: "KYC Verified", value: 0, color: "#6fffd2" },
] : [])
function statusClass(s: string) {
  return { active: "badge-teal", pending: "badge-amber", rejected: "badge-red" }[s] || "badge-cyan"
}
</script>