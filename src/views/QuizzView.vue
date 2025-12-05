<script setup lang="ts">
import { computed, ref } from 'vue'

const quizQuestions = ref([
  {
    text: 'Quel pourcentage de vos logiciels sont libres et open-source ?',
    options: [
      { text: '0-25%', score: 0 },
      { text: '25-50%', score: 33 },
      { text: '50-75%', score: 66 },
      { text: '75-100%', score: 100 },
    ],
  },
  {
    text: 'Avez-vous un programme de reconditionnement de matériel ?',
    options: [
      { text: 'Non, pas du tout', score: 0 },
      { text: 'En réflexion', score: 33 },
      { text: 'En phase pilote', score: 66 },
      { text: 'Oui, déployé', score: 100 },
    ],
  },
  {
    text: 'Vos données sont-elles hébergées localement ?',
    options: [
      { text: 'Tout dans le cloud propriétaire', score: 0 },
      { text: 'Mixte cloud/local', score: 50 },
      { text: 'Majoritairement local', score: 75 },
      { text: 'Tout en local', score: 100 },
    ],
  },
])

const answers = ref<number[]>([])
const autonomyScore = computed(() => {
  if (answers.value.length === 0) return 0
  const sum = answers.value.reduce((a, b) => a + b, 0)
  return Math.round(sum / answers.value.length)
})

const getProfileLevel = computed(() => {
  const score = autonomyScore.value
  if (score < 25) return 'Débutant'
  if (score < 50) return 'Initié'
  if (score < 75) return 'Avancé'
  return 'Expert NIRD'
})

const getProfileDescription = computed(() => {
  const score = autonomyScore.value
  if (score < 25)
    return 'Vous commencez votre parcours. Explorez les solutions faciles pour démarrer !'
  if (score < 50) return "Bon début ! Continuez avec l'expérimentation de nouvelles solutions."
  if (score < 75)
    return "Excellent progrès ! Vous êtes sur la bonne voie vers l'autonomie complète."
  return 'Bravo ! Vous êtes un modèle NIRD. Partagez votre expérience avec la communauté !'
})

function updateScore(questionIndex: number, score: number) {
  answers.value[questionIndex] = score
}
</script>

<template>
  <div class="w-screen overflow-x-hidden flex items-center">
    <section
      id="form"
      class="w-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white py-12"
    >
      <div class="container max-w-4xl">
        <h2 class="text-4xl font-bold text-center mb-6">Calculez Votre Niveau d'Autonomie</h2>
        <p class="text-center text-gray-400 mb-12">
          Répondez à ces questions pour évaluer votre progression NIRD
        </p>

        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10">
          <div class="space-y-6">
            <div v-for="(question, index) in quizQuestions" :key="index">
              <p class="font-semibold mb-3">{{ question.text }}</p>
              <div class="space-y-2">
                <label
                  v-for="(option, optIndex) in question.options"
                  :key="optIndex"
                  class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition-all"
                >
                  <input
                    type="radio"
                    :name="'question-' + index"
                    :value="option.score"
                    @change="updateScore(index, option.score)"
                    class="mr-3"
                  />
                  <span class="text-sm">{{ option.text }}</span>
                </label>
              </div>
            </div>
          </div>

          <div
            class="mt-8 p-6 bg-gradient-to-r from-blue-900/50 to-green-900/50 rounded-xl border border-blue-500/30 flex items-center"
          >
            <div class="flex-1">
              <h4 class="font-bold text-lg mb-2">🎯 Votre Profil: {{ getProfileLevel }}</h4>
              <p class="text-sm text-gray-300">{{ getProfileDescription }}</p>
            </div>
            <div
              class="rounded-full w-20 h-20 border-2 top-3 right-0 flex items-center justify-center"
            >
              <span class="text-3xl font-bold">{{ autonomyScore }}%</span>
            </div>
          </div>
          <div class="mb-8">
            <div class="flex justify-between text-sm text-gray-400 mb-2 pt-6">
              <span>Niveau Débutant</span>
              <span>Niveau Expert</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-4">
              <div
                class="bg-gradient-to-r from-blue-500 via-green-500 to-purple-500 h-4 rounded-full transition-all duration-500"
                :style="{ width: autonomyScore + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped></style>
