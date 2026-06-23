<template>
  <!-- Mobile hamburger button (shown in MainLayout) -->
  <button @click="open = true" class="md:hidden flex flex-col gap-1.5 p-2 rounded-lg transition" style="background:rgba(255,255,255,0.04)" aria-label="Menu">
    <span class="block w-5 h-0.5 rounded transition-all" :style="open ? 'transform:rotate(45deg) translateY(6px);background:#6fffd2' : 'background:#c5d3e4'"></span>
    <span class="block w-4 h-0.5 rounded transition-all ml-auto" :style="open ? 'opacity:0' : 'background:#c5d3e4'"></span>
    <span class="block w-5 h-0.5 rounded transition-all" :style="open ? 'transform:rotate(-45deg) translateY(-6px);background:#6fffd2' : 'background:#c5d3e4'"></span>
  </button>

  <!-- Overlay -->
  <Transition name="fade">
    <div v-if="open" @click="open = false" class="md:hidden fixed inset-0 z-40" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)"></div>
  </Transition>

  <!-- Slide-in panel -->
  <Transition name="slide">
    <div v-if="open" class="md:hidden fixed top-0 right-0 bottom-0 z-50 w-72 flex flex-col" style="background:#080d13;border-left:1px solid rgba(111,255,210,0.15)">
      <!-- Header -->
      <div class="flex items-center justify-between p-5" style="border-bottom:1px solid rgba(255,255,255,0.06)">
        <span class="font-black text-lg" style="color:#f0f6fc">Hub<span class="gradient-text">Price</span><span style="color:#6fffd2">.AI</span></span>
        <button @click="open = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-lg" style="background:rgba(255,255,255,0.05);color:#6e7f96">×</button>
      </div>

      <!-- Nav links -->
      <nav class="flex-1 p-5 space-y-1">
        <RouterLink v-for="link in links" :key="link.to" :to="link.to" @click="open = false"
          class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all"
          :style="$route.path.startsWith(link.to) ? 'background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.15)' : 'color:#c5d3e4;border:1px solid transparent'">
          <span>{{ link.icon }}</span>{{ link.label }}
        </RouterLink>
      </nav>

      <!-- Auth buttons -->
      <div class="p-5 space-y-3" style="border-top:1px solid rgba(255,255,255,0.06)">
        <template v-if="auth.isAuthenticated">
          <RouterLink :to="auth.isAdmin ? '/admin' : '/dashboard'" @click="open = false" class="block w-full text-center py-3 rounded-xl text-sm font-bold" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">Dashboard</RouterLink>
          <button @click="logout" class="block w-full text-center py-3 rounded-xl text-sm font-medium" style="color:#6e7f96;border:1px solid rgba(255,255,255,0.08)">Sign Out</button>
        </template>
        <template v-else>
          <RouterLink to="/register" @click="open = false" class="block w-full text-center py-3 rounded-xl text-sm font-bold" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">Get Started Free</RouterLink>
          <RouterLink to="/login" @click="open = false" class="block w-full text-center py-3 rounded-xl text-sm font-medium" style="color:#c5d3e4;border:1px solid rgba(255,255,255,0.1)">Sign In</RouterLink>
        </template>
      </div>
    </div>
  </Transition>
</template>
<script setup lang="ts">
import { ref } from "vue"; import { RouterLink, useRouter } from "vue-router"; import { useAuthStore } from "@/stores/auth"
const open = ref(false); const auth = useAuthStore(); const router = useRouter()
const links = [
  { to: "/cars", label: "Cars", icon: "🚗" },
  { to: "/properties", label: "Properties", icon: "🏠" },
  { to: "/rentals", label: "Rentals", icon: "🔑" },
  { to: "/investment-center", label: "Investment Center", icon: "📈" },
  { to: "/market-insights", label: "Market Insights", icon: "📊" },
  { to: "/search", label: "Advanced Search", icon: "🔍" },
]
async function logout() { await auth.logout(); open.value = false; router.push("/") }
</script>
<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
</style>