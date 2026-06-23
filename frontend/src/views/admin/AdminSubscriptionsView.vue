<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black mb-1" style="color:#f0f6fc">Subscription <span class="gradient-text">Management</span></h1>
        <p class="text-sm" style="color:#6e7f96">Manage plans, view subscribers and revenue</p>
      </div>
      <button @click="showAddPlan=true" class="px-5 py-2.5 text-sm font-semibold rounded-xl" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">+ New Plan</button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="s in statsCards" :key="s.label" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
        <p class="text-2xl font-black mb-1" :style="{color:s.color}">{{ s.value }}</p>
        <p class="text-xs" style="color:#6e7f96">{{ s.label }}</p>
      </div>
    </div>

    <!-- Plans -->
    <div class="mb-8">
      <h2 class="font-bold mb-4" style="color:#f0f6fc">Subscription Plans</h2>
      <div v-if="loadingPlans" class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-40 rounded-2xl animate-pulse" style="background:#0d1117"></div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="plan in plans" :key="plan.id" class="rounded-2xl p-5" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="font-black text-base" style="color:#f0f6fc">{{ plan.name }}</p>
              <p class="text-xs mt-0.5" style="color:#6e7f96">{{ plan.type || 'standard' }}</p>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full" :style="plan.is_active?'background:rgba(111,255,210,0.1);color:#6fffd2':'background:rgba(239,68,68,0.1);color:#ef4444'">{{ plan.is_active?'Active':'Off' }}</span>
          </div>
          <p class="text-2xl font-black mb-1" style="color:#6fffd2">£{{ plan.price_monthly }}<span class="text-xs font-normal" style="color:#6e7f96">/mo</span></p>
          <p class="text-xs mb-3" style="color:#6e7f96">Up to {{ plan.max_listings }} listings</p>
          <div class="flex gap-2">
            <button @click="openEditPlan(plan)" class="flex-1 py-1.5 text-xs rounded-lg" style="background:rgba(86,216,255,0.08);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">Edit</button>
            <button @click="deletePlan(plan.id)" class="py-1.5 px-3 text-xs rounded-lg" style="background:rgba(239,68,68,0.06);color:#ef4444;border:1px solid rgba(239,68,68,0.15)">Del</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Subscriptions table -->
    <div class="rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(255,255,255,0.07)">
      <div class="flex items-center justify-between mb-5">
        <h2 class="font-bold" style="color:#f0f6fc">Active Subscribers</h2>
        <div class="flex gap-3">
          <select v-model="subFilter" @change="fetchSubs(1)" class="px-3 py-1.5 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="trial">Trial</option>
            <option value="cancelled">Cancelled</option>
            <option value="expired">Expired</option>
          </select>
          <button @click="showAssign=true" class="px-4 py-1.5 text-xs rounded-lg font-medium" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.2)">+ Assign Plan</button>
        </div>
      </div>
      <div v-if="loadingSubs" class="space-y-2">
        <div v-for="i in 5" :key="i" class="h-14 rounded-xl animate-pulse" style="background:rgba(255,255,255,0.04)"></div>
      </div>
      <div v-else-if="subs.length===0" class="text-center py-12" style="color:#6e7f96">No subscriptions found.</div>
      <div v-else class="space-y-2">
        <div v-for="s in subs" :key="s.id" class="flex items-center justify-between p-4 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05)">
          <div>
            <p class="text-sm font-semibold" style="color:#f0f6fc">{{ s.user?.name }}</p>
            <p class="text-xs" style="color:#6e7f96">{{ s.user?.email }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span class="text-xs px-2 py-1 rounded-full font-medium" style="background:rgba(111,255,210,0.08);color:#6fffd2;border:1px solid rgba(111,255,210,0.15)">{{ s.plan?.name }}</span>
            <span class="text-xs font-semibold" style="color:#f0f6fc">£{{ s.amount }}/mo</span>
            <span class="text-xs px-2 py-0.5 rounded-full capitalize" :style="statusSty(s.status)">{{ s.status }}</span>
            <span class="text-xs" style="color:#6e7f96">{{ s.current_period_end ? new Date(s.current_period_end).toLocaleDateString('en-GB') : '-' }}</span>
            <button @click="editSub(s)" class="px-3 py-1 text-xs rounded-lg" style="background:rgba(86,216,255,0.08);color:#56d8ff;border:1px solid rgba(86,216,255,0.2)">Edit</button>
            <button @click="cancelSub(s.id)" class="px-3 py-1 text-xs rounded-lg" style="background:rgba(239,68,68,0.08);color:#ef4444;border:1px solid rgba(239,68,68,0.15)">Cancel</button>
          </div>
        </div>
      </div>
      <div v-if="subLastPage>1" class="flex gap-2 justify-center mt-4">
        <button v-for="p in subLastPage" :key="p" @click="fetchSubs(p)" class="w-8 h-8 rounded-lg text-xs" :style="p===subPage?'background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b':'background:rgba(255,255,255,0.04);color:#6e7f96'">{{ p }}</button>
      </div>
    </div>

    <!-- Add/Edit Plan Modal -->
    <div v-if="showAddPlan||editingPlan" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.75)" @click.self="showAddPlan=false;editingPlan=null">
      <div class="w-full max-w-md rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.25)">
        <h2 class="font-bold mb-5" style="color:#f0f6fc">{{ editingPlan?'Edit Plan':'Add Plan' }}</h2>
        <div class="space-y-3">
          <input v-model="planForm.name" placeholder="Plan name (e.g. Professional)" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
          <textarea v-model="planForm.description" placeholder="Description" rows="2" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc"></textarea>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">Monthly Price (£)</label>
              <input v-model="planForm.price_monthly" type="number" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
            </div>
            <div>
              <label class="text-xs mb-1 block" style="color:#6e7f96">Max Listings</label>
              <input v-model="planForm.max_listings" type="number" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
            </div>
          </div>
          <div class="flex gap-4">
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc"><input type="checkbox" v-model="planForm.ai_price_analysis" /> AI Analysis</label>
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc"><input type="checkbox" v-model="planForm.featured_listing" /> Featured</label>
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#f0f6fc"><input type="checkbox" v-model="planForm.is_active" /> Active</label>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="savePlan" :disabled="saving" class="flex-1 py-3 rounded-xl font-bold text-sm disabled:opacity-50" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ saving?'Saving...':'Save Plan' }}</button>
          <button @click="showAddPlan=false;editingPlan=null" class="flex-1 py-3 rounded-xl text-sm" style="border:1px solid rgba(255,255,255,0.1);color:#c5d3e4">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Assign Plan Modal -->
    <div v-if="showAssign" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.75)" @click.self="showAssign=false">
      <div class="w-full max-w-md rounded-2xl p-6" style="background:#0d1117;border:1px solid rgba(111,255,210,0.25)">
        <h2 class="font-bold mb-5" style="color:#f0f6fc">Assign Subscription Plan</h2>
        <div class="space-y-3">
          <input v-model="assignForm.user_id" placeholder="User UUID" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc" />
          <select v-model="assignForm.plan_id" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
            <option value="">Select Plan</option>
            <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} - £{{ p.price_monthly }}/mo</option>
          </select>
          <select v-model="assignForm.billing_cycle" class="w-full px-4 py-3 rounded-xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#f0f6fc">
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="assignPlan" :disabled="saving" class="flex-1 py-3 rounded-xl font-bold text-sm disabled:opacity-50" style="background:linear-gradient(135deg,#6fffd2,#56d8ff);color:#05070b">{{ saving?'Assigning...':'Assign Plan' }}</button>
          <button @click="showAssign=false" class="flex-1 py-3 rounded-xl text-sm" style="border:1px solid rgba(255,255,255,0.1);color:#c5d3e4">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import client from '@/api/client'
const plans = ref<any[]>([])
const subs = ref<any[]>([])
const statsData = ref<any>({})
const loadingPlans = ref(true)
const loadingSubs = ref(true)
const saving = ref(false)
const showAddPlan = ref(false)
const showAssign = ref(false)
const editingPlan = ref<any>(null)
const subFilter = ref('')
const subPage = ref(1)
const subLastPage = ref(1)
const planForm = ref({ name:'', description:'', price_monthly:0, max_listings:10, ai_price_analysis:false, featured_listing:false, is_active:true })
const assignForm = ref({ user_id:'', plan_id:'', billing_cycle:'monthly' })

const statsCards = computed(() => [
  { label:'Active Subscriptions', value: statsData.value.total_active||0, color:'#6fffd2' },
  { label:'Monthly Revenue', value: '£'+(statsData.value.total_revenue||0).toLocaleString(), color:'#56d8ff' },
  { label:'New This Month', value: statsData.value.new_this_month||0, color:'#a78bfa' },
  { label:'Cancelled This Month', value: statsData.value.cancelled_this_month||0, color:'#fbbf24' },
])

onMounted(async () => {
  await Promise.all([fetchPlans(), fetchSubs(1), fetchStats()])
})

async function fetchPlans() {
  loadingPlans.value = true
  try { const r = await client.get('/admin/subscription-plans'); plans.value = r.data || [] }
  catch { plans.value = [] }
  finally { loadingPlans.value = false }
}

async function fetchSubs(page = 1) {
  loadingSubs.value = true
  try {
    const r = await client.get('/admin/subscriptions', { params: { status: subFilter.value, page } })
    subs.value = r.data.data || []
    subPage.value = r.data.current_page || 1
    subLastPage.value = r.data.last_page || 1
  } catch { subs.value = [] }
  finally { loadingSubs.value = false }
}

async function fetchStats() {
  try { const r = await client.get('/admin/subscriptions/stats'); statsData.value = r.data } catch {}
}

function openEditPlan(p: any) {
  editingPlan.value = p
  planForm.value = { name:p.name, description:p.description||'', price_monthly:p.price_monthly, max_listings:p.max_listings, ai_price_analysis:!!p.ai_price_analysis, featured_listing:!!p.featured_listing, is_active:p.is_active!==false }
}

async function savePlan() {
  saving.value = true
  try {
    if (editingPlan.value) {
      await client.put('/admin/subscription-plans/'+editingPlan.value.id, planForm.value)
    } else {
      await client.post('/admin/subscription-plans', planForm.value)
    }
    showAddPlan.value = false; editingPlan.value = null
    planForm.value = { name:'', description:'', price_monthly:0, max_listings:10, ai_price_analysis:false, featured_listing:false, is_active:true }
    await fetchPlans(); await fetchStats()
  } catch(e:any) { alert(e?.response?.data?.message||'Failed') }
  finally { saving.value = false }
}

async function deletePlan(id: string) {
  if (!confirm('Delete this plan?')) return
  await client.delete('/admin/subscription-plans/'+id)
  await fetchPlans()
}

async function cancelSub(id: string) {
  if (!confirm('Cancel this subscription?')) return
  await client.delete('/admin/subscriptions/'+id)
  await fetchSubs(subPage.value); await fetchStats()
}

function editSub(s: any) {
  const newStatus = prompt('New status (active/trial/cancelled/expired):', s.status)
  if (!newStatus) return
  client.put('/admin/subscriptions/'+s.id, { status: newStatus }).then(() => fetchSubs(subPage.value))
}

async function assignPlan() {
  if (!assignForm.value.plan_id || !assignForm.value.user_id) return alert('Fill all fields')
  saving.value = true
  try {
    await client.post('/admin/subscriptions', { ...assignForm.value, status: 'active' })
    showAssign.value = false
    assignForm.value = { user_id:'', plan_id:'', billing_cycle:'monthly' }
    await fetchSubs(1); await fetchStats()
  } catch(e:any) { alert(e?.response?.data?.message||'Failed') }
  finally { saving.value = false }
}

function statusSty(s: string) {
  const m: Record<string,string> = { active:'background:rgba(111,255,210,0.1);color:#6fffd2', trial:'background:rgba(86,216,255,0.1);color:#56d8ff', cancelled:'background:rgba(239,68,68,0.1);color:#ef4444', expired:'background:rgba(255,255,255,0.05);color:#6e7f96' }
  return m[s] || m.expired
}
</script>