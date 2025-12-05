<template>
  <div class="w-full max-w-xl mx-auto overflow-hidden">
    <div class="relative h-48">
      <div
        class="flex h-full transition-transform duration-700 ease-in-out"
        :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
      >
        <div
          v-for="(audience, index) in extendedAudiences"
          :key="audience.title + index"
          class="w-full flex-shrink-0 flex flex-col items-center justify-center bg-white/5 rounded-2xl border border-white/10 px-6 py-4"
        >
          <component :is="audience.icon" :size="48" class="mb-2 text-blue-400" />
          <h3 class="text-xl font-bold mb-1">{{ audience.title }}</h3>
          <p class="text-sm text-gray-300 text-center">
            {{ audience.description }}
          </p>
        </div>
      </div>

      <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-2">
        <button
          v-for="(a, idx) in audiences"
          :key="a.title + '-dot'"
          @click="goTo(idx)"
          class="w-2 h-2 rounded-full transition-all"
          :class="idx === currentIndex % audiences.length ? 'bg-white w-4' : 'bg-white/40'"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { GraduationCap, Group, HomeSimple, Building } from '@iconoir/vue'

const audiences = ref([
  { icon: GraduationCap, title: 'Élèves', description: 'Devenir acteurs du numérique' },
  { icon: Group, title: 'Enseignants', description: 'Gagner en autonomie technologique' },
  { icon: HomeSimple, title: 'Familles', description: 'Comprendre les enjeux' },
  { icon: Building, title: 'Collectivités', description: "Réduire les coûts et l'impact" },
])

const extendedAudiences = computed(() => [...audiences.value, ...audiences.value])

const currentIndex = ref(0)
let intervalId: number | undefined

function next() {
  currentIndex.value = (currentIndex.value + 1) % extendedAudiences.value.length
}

function goTo(index: number) {
  currentIndex.value = index
}

onMounted(() => {
  intervalId = window.setInterval(next, 2500)
})

onBeforeUnmount(() => {
  if (intervalId) window.clearInterval(intervalId)
})
</script>
