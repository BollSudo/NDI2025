<template>
  <div
    class="min-h-screen w-full overflow-x-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white flex flex-col gap-2 items-center justify-center"
  >
    <section id="pillars" class="py-12">
      <div class="container px-6">
        <h2 class="text-4xl font-bold text-center mb-16">Les piliers</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div
            v-for="(pillar, index) in pillars"
            :key="index"
            class="bg-white/5 backdrop-blur-lg rounded-2xl transition-all duration-300 ease-out cursor-pointer hover:bg-white/10 hover:shadow-xl hover:border-white/20 transition-eas"
          >
            <InfoCard :pillar="pillar" />
          </div>
        </div>
      </div>
    </section>

    <section id="solutions" class="py-12">
      <div class="container px-6">
        <h2 class="text-4xl font-bold text-center mb-16">Les solutions</h2>
        <div class="w-full flex flex-wrap gap-2 items-center justify-center">
          <div
            v-for="solution in solutions"
            :key="solution.title"
            class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur rounded-xl p-6 border border-white/10 hover:border-blue-500/50 transition-all hover:transform cursor-pointer group"
          >
            <div class="flex w-full items-start justify-between mb-4">
              <component :is="solution.icon" class="text-3xl text-blue-400" size="32" />
              <span
                class="px-3 py-1 rounded-full text-xs font-semibold"
                :class="
                  solution.difficulty === 'Facile'
                    ? 'bg-green-500/20 text-green-400'
                    : solution.difficulty === 'Moyen'
                      ? 'bg-yellow-500/20 text-yellow-400'
                      : 'bg-red-500/20 text-red-400'
                "
              >
                {{ solution.difficulty }}
              </span>
            </div>
            <h3 class="text-xl font-bold mb-2 group-hover:text-blue-400 transition-colors">
              {{ solution.title }}
            </h3>
            <p class="text-sm text-gray-400 mb-4">{{ solution.description }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
              <span>⚡ Impact: {{ solution.impact }}</span>
              <span>⏱️ {{ solution.duration }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="cibles" class="py-12">
      <h2 class="text-4xl font-bold text-center mb-16">Les acteurs</h2>
      <SlidingCard />
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import pillars from '@/constants/pillars.ts'
import InfoCard from '@/components/InfoCard.vue'
import SlidingCard from '@/components/SlidingCard.vue'
import {
  LaptopDevMode,
  Wrench,
  BookStack,
  Server,
  SmartphoneDevice,
  GraduationCap,
} from '@iconoir/vue'

const solutions = ref([
  {
    icon: LaptopDevMode,
    title: 'Migration vers Linux',
    description: "Installer un système libre sur les ordinateurs de l'établissement",
    difficulty: 'Moyen',
    impact: 'Élevé',
    duration: '2-3 mois',
  },
  {
    icon: Wrench,
    title: 'Atelier reconditionnement',
    description: 'Former les élèves à réparer et reconditionner du matériel',
    difficulty: 'Facile',
    impact: 'Élevé',
    duration: '1 mois',
  },
  {
    icon: BookStack,
    title: 'Logiciels libres',
    description: 'Remplacer les suites propriétaires (LibreOffice, GIMP...)',
    difficulty: 'Facile',
    impact: 'Moyen',
    duration: '2 semaines',
  },
  {
    icon: Server,
    title: 'Serveur local',
    description: "Héberger les données de l'établissement en local",
    difficulty: 'Avancé',
    impact: 'Élevé',
    duration: '3-6 mois',
  },
  {
    icon: SmartphoneDevice,
    title: 'Apps alternatives',
    description: 'Utiliser des alternatives libres (Nextcloud, Jitsi...)',
    difficulty: 'Facile',
    impact: 'Moyen',
    duration: '1 mois',
  },
  {
    icon: GraduationCap,
    title: 'Formation équipes',
    description: 'Workshops réguliers sur les outils libres',
    difficulty: 'Facile',
    impact: 'Élevé',
    duration: 'Continu',
  },
])
</script>

<style scoped></style>
