<template>
  <div class="flex min-h-screen" style="background:#05070b">
    <!-- Sidebar -->
    <aside class="w-64 shrink-0 flex flex-col" style="background:#0d1117;border-right:1px solid rgba(255,255,255,0.06)">
      <div class="p-5 flex items-center gap-2.5" style="border-bottom:1px solid rgba(255,255,255,0.06)">
        <RouterLink to="/" class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">H</div>
          <span class="font-black text-sm" style="color:#f0f6fc">Hub<span class="gradient-text">Price</span></span>
        </RouterLink>
      </div>
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <p class="text-xs font-bold px-3 mb-2 mt-2" style="color:#6e7f96;letter-spacing:0.08em">DASHBOARD</p>
        <SidebarLink to="/dashboard/overview" icon="grid">Overview</SidebarLink>
        <SidebarLink to="/dashboard/listings" icon="list">My Listings</SidebarLink>
        <SidebarLink to="/dashboard/listings/new" icon="plus">New Listing</SidebarLink>
        <SidebarLink to="/dashboard/messages" icon="message">Messages</SidebarLink>
        <SidebarLink to="/dashboard/analytics" icon="chart">Analytics</SidebarLink>
        <SidebarLink to="/dashboard/favorites" icon="heart">Favourites</SidebarLink>
        <p class="text-xs font-bold px-3 mb-2 mt-4" style="color:#6e7f96;letter-spacing:0.08em">ACCOUNT</p>
        <SidebarLink to="/dashboard/profile" icon="user">Profile</SidebarLink>
        <SidebarLink to="/dashboard/kyc" icon="shield">KYC Verification</SidebarLink>
        <SidebarLink to="/dashboard/subscription" icon="star">Subscription</SidebarLink>
      </nav>
      <div class="p-4" style="border-top:1px solid rgba(255,255,255,0.06)">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ auth.user?.name?.charAt(0) }}</div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold truncate" style="color:#f0f6fc">{{ auth.user?.name }}</p>
            <p class="text-xs truncate" style="color:#6e7f96">{{ auth.user?.role }}</p>
          </div>
        </div>
        <button @click="logout" class="w-full py-2 rounded-lg text-xs font-medium transition" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:#6e7f96">Sign Out</button>
      </div>
    </aside>
    <!-- Content -->
    <main class="flex-1 p-8 overflow-auto">
      <RouterView />
    </main>
  </div>
</template>
<script setup lang="ts">
import { RouterLink, RouterView, useRouter } from "vue-router"
import { useAuthStore } from "@/stores/auth"
import SidebarLink from "@/components/ui/SidebarLink.vue"
const auth = useAuthStore(); const router = useRouter()
async function logout() { await auth.logout(); router.push("/") }
</script>