<template>
  <svg :width="size" :height="size" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="HubPrice.AI">
    <defs>
      <linearGradient :id="gid" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="#00BFFF" />
        <stop offset="1" stop-color="#00E6B0" />
      </linearGradient>
    </defs>
    <rect v-if="badge" x="2" y="2" width="116" height="116" :rx="rx" fill="#0E1014" :stroke="'url(#' + gid + ')'" stroke-width="2" />
    <g transform="translate(18,12) scale(0.84)">
      <path d="M50 8 L88 21 V52 C88 74 71 88 50 95 C29 88 12 74 12 52 V21 Z" fill="none" :stroke="accent" stroke-opacity="0.18" stroke-width="1.4" />
      <rect x="20" y="30" width="11" height="48" rx="2.5" :fill="'url(#' + gid + ')'" />
      <rect x="61" y="24" width="11" height="54" rx="2.5" :fill="'url(#' + gid + ')'" />
      <path d="M72 27 H80 C90.5 27 90.5 45 80 45 H72" fill="none" :stroke="'url(#' + gid + ')'" stroke-width="9" stroke-linecap="round" />
      <polyline points="25,62 39,55 52,49 66,40" fill="none" :stroke="accent" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
      <circle cx="25" cy="62" r="3.2" fill="#00E6B0" />
      <circle cx="39" cy="55" r="2.4" :fill="accent" />
      <circle cx="52" cy="49" r="2.4" :fill="accent" />
      <circle cx="66" cy="40" r="3.6" fill="#00BFFF" />
    </g>
  </svg>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  size?: number
  badge?: boolean
  dark?: boolean
}>(), {
  size: 34,
  badge: true,
  dark: false,
})

const gid = computed(() => 'hpg-' + Math.random().toString(36).slice(2, 8))
const rx = computed(() => Math.round(props.size * 0.27))
const accent = computed(() => (props.dark ? '#0A0A0A' : '#FFFFFF'))
</script>
