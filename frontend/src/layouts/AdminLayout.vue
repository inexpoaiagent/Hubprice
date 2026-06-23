<template>
  <div class='flex min-h-screen' style='background:#05070b'>
    <aside class='w-64 shrink-0 flex flex-col' style='background:#080d13;border-right:1px solid rgba(111,255,210,0.1)'>
      <div class='p-5' style='border-bottom:1px solid rgba(255,255,255,0.06)'>
        <RouterLink to='/' class='flex items-center gap-2 mb-2'>
          <div class='w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs' style='background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b'>H</div>
          <span class='font-black text-sm' style='color:#f0f6fc'>HubPrice<span style='color:#6fffd2'>.AI</span></span>
        </RouterLink>
        <span class='text-xs px-2 py-0.5 rounded-full font-semibold' style='background:rgba(111,255,210,0.1);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)'>Admin Panel</span>
      </div>
      <nav class='flex-1 p-3 overflow-y-auto'>
        <p class='nav-section'>MAIN</p>
        <SideLink to='/admin/overview'>Overview</SideLink>
        <SideLink to='/admin/live-analytics'>Live Analytics</SideLink>
        <SideLink to='/admin/ai-insights'>AI Insights</SideLink>
        <p class='nav-section'>MARKETPLACE</p>
        <SideLink to='/admin/listings'>All Listings</SideLink>
        <SideLink to='/admin/vehicles'>Vehicle &amp; Property</SideLink>
        <SideLink to='/admin/users'>Users</SideLink>
        <SideLink to='/admin/agencies'>Agencies &amp; Dealers</SideLink>
        <p class='nav-section'>FINANCE</p>
        <SideLink to='/admin/financial'>Financial</SideLink>
        <SideLink to='/admin/market-analytics'>Market Analytics</SideLink>
        <SideLink to='/admin/subscriptions'>Subscriptions</SideLink>
        <SideLink to='/admin/advertisements'>Advertisements</SideLink>
        <p class='nav-section'>SAFETY & CONTENT</p>
        <SideLink to='/admin/fraud'>Fraud Detection</SideLink>
        <SideLink to='/admin/reports'>Reports</SideLink>
        <SideLink to='/admin/content'>Content</SideLink>
        <SideLink to='/admin/notifications'>Notifications</SideLink>
        <SideLink to='/admin/competitors'>Competitors</SideLink>
        <SideLink to='/admin/settings'>Settings</SideLink>
        <p class='nav-section'>AI / LLM</p>
        <SideLink to='/admin/llm-management'>LLM Management</SideLink>
        <SideLink to='/admin/ai-intelligence'>AI Intelligence</SideLink>
      </nav>
      <div class='p-4' style='border-top:1px solid rgba(255,255,255,0.06)'>
        <div class='flex items-center gap-3 mb-3'>
          <div class='w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0' style='background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b'>
            {{ auth.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
          </div>
          <div class='flex-1 min-w-0'>
            <p class='text-xs font-semibold truncate' style='color:#f0f6fc'>{{ auth.user?.name || 'Admin' }}</p>
            <p class='text-xs' style='color:#6fffd2'>Super Admin</p>
          </div>
        </div>
        <button @click='logout' class='w-full py-2 rounded-lg text-xs font-medium' style='background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.15);color:#ef4444'>Sign Out</button>
      </div>
    </aside>
    <main class='flex-1 overflow-auto' style='background:#05070b'><RouterView /></main>
  </div>
</template>
<script setup lang='ts'>
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { h, defineComponent, computed } from 'vue'

const route = useRoute()
const auth = useAuthStore()
const router = useRouter()

const SideLink = defineComponent({
  props: { to: { type: String, required: true } },
  setup(props, { slots }) {
    const isActive = computed(() => {
      const p = route.path
      return p === props.to || (p.startsWith(props.to + '/') && props.to !== '/admin')
    })
    return () => h(RouterLink, {
      to: props.to,
      class: 'flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all mb-0.5 w-full',
      style: isActive.value
        ? 'background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.15)'
        : 'color:#6e7f96;border:1px solid transparent'
    }, slots.default)
  }
})

async function logout() { await auth.logout(); router.push('/') }
</script>
<style scoped>
.nav-section { font-size:10px;font-weight:700;letter-spacing:0.1em;color:#2a3a4a;padding:16px 12px 6px;text-transform:uppercase; }
</style>