import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '@/api/client'
import type { MarketStats, VehicleBrand, PropertyType, SubscriptionPlan } from '@/types'

export const useMarketStore = defineStore('market', () => {
  const stats = ref<MarketStats | null>(null)
  const vehicleBrands = ref<VehicleBrand[]>([])
  const propertyTypes = ref<PropertyType[]>([])
  const subscriptionPlans = ref<SubscriptionPlan[]>([])

  async function fetchStats() {
    const res = await client.get('/market/stats')
    stats.value = res.data
  }

  async function fetchVehicleBrands() {
    if (vehicleBrands.value.length > 0) return
    const res = await client.get('/vehicle-brands')
    vehicleBrands.value = res.data
  }

  async function fetchPropertyTypes() {
    if (propertyTypes.value.length > 0) return
    const res = await client.get('/property-types')
    propertyTypes.value = res.data
  }

  async function fetchSubscriptionPlans() {
    const res = await client.get('/subscription-plans')
    subscriptionPlans.value = res.data
  }

  return { stats, vehicleBrands, propertyTypes, subscriptionPlans, fetchStats, fetchVehicleBrands, fetchPropertyTypes, fetchSubscriptionPlans }
})
