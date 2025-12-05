<script setup lang="ts">
import RotatingModel from '@/components/RotatingModel.vue'

interface Pillar {
  icon: string
  model: string
  camera: [number, number, number]
  ambientLightIntensity: number
  title: string
  color: string
  description: string
  actions: string[]
}

defineProps<{
  pillar: Pillar
}>()
</script>

<template>
  <div class="flex flex-col items-center gap-2 w-full py-4 px-4">
    <div class="text-5xl mb-4 w-50 h-50">
      <Suspense>
        <RotatingModel
          :model-path="pillar.model"
          :camera-position="pillar.camera"
          :ambient-light-intensity="pillar.ambientLightIntensity"
        ></RotatingModel>
        <template #fallback>
          <div class="text-5xl mb-4">{{ pillar.icon }}</div>
        </template>
      </Suspense>
    </div>
    <div>
      <h3 class="text-2xl font-bold mb-4" :class="pillar.color">{{ pillar.title }}</h3>
      <p class="text-gray-300">{{ pillar.description }}</p>
      <div v-for="item in pillar.actions" :key="item" class="">
        <p class="text-x text-gray-400">{{ item }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
