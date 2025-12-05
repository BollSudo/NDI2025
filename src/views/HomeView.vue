<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import CircularNavbar from '@/components/CircularNavbar.vue'
import TypeWriter from '@/components/TypeWriter.vue'

const showNavbar = ref(false)

const onAnimationComplete = () => {
  showNavbar.value = true
}

// --- Configuration des Diapositives (Slides) ---
const slides = [
  '// BOOT SEQUENCE INITIEE...\n\nANNEE 2025. Le systeme scolaire est envahi par les dependances numeriques...\n\nPerte de controle. Obscurite. Epuisement des ressources.',
  'UNE ANCIENNE PROPHETIE parle de la demarche NIRD (Numerique Inclusif, Responsable et Durable).',
  'NIRD est la cle. Elle promet autonomie, durabilite et equite. Mais le chemin est long. Il faut une carte. Il faut un GUIDE.',
  "BIENVENUE, HEROS. Ta mission est d'aider les etablissements a PASSER AU NIVEAU SUPERIEUR.\n\nEs-tu pret a debute ta quete ?",
]
const totalSlides = slides.length

// --- États Réactifs ---
const currentSlideIndex = ref(0)
const isTyping = ref(true)
const isFinished = ref(false)

// --- Computed Properties pour l'UI ---
const currentTextForTypewriter = computed(() => slides[currentSlideIndex.value])

const progressPercentage = computed(() => {
  return ((currentSlideIndex.value + 1) / totalSlides) * 100
})

const instructionMessage = computed(() => {
  return currentSlideIndex.value < totalSlides - 1
    ? '> APPUYER SUR [ESPACE] POUR CONTINUER <'
    : "> APPUYER SUR [ESPACE] POUR ENTRER DANS L'APPLICATION <"
})

const showInstruction = computed(() => {
  return !isTyping.value && !isFinished.value
})

// --- Logique de Progression ---
const handleTypingFinished = () => {
  isTyping.value = false
}

const nextSlide = () => {
  if (isTyping.value) {
    return
  }

  if (currentSlideIndex.value >= totalSlides - 1) {
    isFinished.value = true
    onAnimationComplete()
    return
  }

  currentSlideIndex.value++
  isTyping.value = true
}

// --- Gestionnaire d'Événements Clavier ---
const handleKeyPress = (event: KeyboardEvent) => {
  if (event.code === 'Space') {
    event.preventDefault()
    nextSlide()
  }
}

// --- Gestion des Styles Globaux ---
const CRT_FONT_CLASS = 'crt-active-font'
const CRT_SCANLINES_CLASS = 'crt-active-scanlines'
const CRT_FULLSCREEN_CLASS = 'crt-active-fullscreen'

onMounted(() => {
  window.addEventListener('keydown', handleKeyPress)
  document.body.classList.add(CRT_FONT_CLASS, CRT_FULLSCREEN_CLASS)
  const appElement = document.getElementById('app')
  if (appElement) {
    appElement.classList.add(CRT_SCANLINES_CLASS, CRT_FULLSCREEN_CLASS)
  }
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyPress)
  document.body.classList.remove(CRT_FONT_CLASS, CRT_FULLSCREEN_CLASS)
  const appElement = document.getElementById('app')
  if (appElement) {
    appElement.classList.remove(CRT_SCANLINES_CLASS, CRT_FULLSCREEN_CLASS)
  }
})
</script>

<template>
  <main class="relative">
    <!-- Écran cinématique d'intro -->
    <div
      v-if="!isFinished"
      class="fixed inset-0 bg-black text-gray-200 flex justify-center items-center overflow-hidden"
    >
      <!-- Conteneur CRT avec effet rétro -->
      <div
        class="crt-screen w-full max-w-5xl mx-4 md:mx-8 relative p-6 md:p-12 shadow-green-glow min-h-[60vh] flex flex-col justify-between rounded-lg bg-black/90 border border-green-500/30"
      >
        <!-- Section du contenu principal (l'histoire) -->
        <div
          class="intro-text flex-1 flex items-center justify-center text-sm md:text-base lg:text-lg text-center leading-relaxed"
        >
          <TypeWriter
            v-if="!showNavbar"
            :text="currentTextForTypewriter"
            :typing-speed="40"
            @finished="handleTypingFinished"
          />
        </div>

        <!-- HUD Rétro Gaming (en bas) -->
        <div class="mt-8 text-xs md:text-sm pt-6 border-t border-green-700/50">
          <!-- Jauge de Progression -->
          <div
            class="loading-bar-container w-full h-4 mb-3 rounded-sm overflow-hidden border-2 border-green-500 bg-gray-900/80"
          >
            <div
              class="loading-bar-fill h-full bg-gradient-to-r from-green-500 to-green-400 transition-all duration-300 shadow-[0_0_10px_rgba(0,255,0,0.5)]"
              :style="{ width: `${progressPercentage}%` }"
            />
          </div>

          <!-- Compteurs et Version -->
          <div class="flex justify-between items-center font-mono text-green-400 text-shadow-glow">
            <span>{{ `NIRD SEQUENCE ${currentSlideIndex + 1}/${totalSlides}` }}</span>
            <span class="text-green-500/70">NIRD OS v1.0.0 (C) 2025</span>
          </div>

          <!-- Instruction (Clignotante) -->
          <div
            :class="[
              'mt-6 text-center text-yellow-300 font-bold transition-opacity duration-300 text-shadow-glow-yellow',
              { 'animate-pulse-slow': showInstruction, 'opacity-0': !showInstruction },
            ]"
          >
            {{ instructionMessage }}
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation circulaire (après la cinématique) -->
    <div v-else class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <circular-navbar />
    </div>
  </main>
</template>

<style scoped>
/* Ombre portée personnalisée pour le glow du conteneur */
.shadow-green-glow {
  box-shadow:
    0 0 20px rgba(0, 255, 0, 0.4),
    0 0 40px rgba(0, 255, 0, 0.2),
    inset 0 0 15px rgba(0, 255, 0, 0.2);
}

/* Effet de glow pour le texte principal */
.intro-text {
  text-shadow:
    0 0 5px rgba(0, 255, 0, 0.8),
    0 0 10px rgba(0, 255, 0, 0.6),
    0 0 15px rgba(0, 255, 0, 0.4);
}

/* Text shadow pour les éléments HUD */
.text-shadow-glow {
  text-shadow:
    0 0 3px rgba(0, 255, 0, 0.8),
    0 0 6px rgba(0, 255, 0, 0.5);
}

.text-shadow-glow-yellow {
  text-shadow:
    0 0 5px rgba(255, 255, 0, 0.8),
    0 0 10px rgba(255, 255, 0, 0.6);
}

/* Animation de clignotement pour les instructions */
.animate-pulse-slow {
  animation: slow-pulse 1.5s ease-in-out infinite;
}

@keyframes slow-pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.3;
    transform: scale(0.98);
  }
}

/* Style pour la barre de progression avec effet lumineux */
.loading-bar-fill {
  animation: progress-shimmer 2s ease-in-out infinite;
}

@keyframes progress-shimmer {
  0%,
  100% {
    filter: brightness(1);
  }
  50% {
    filter: brightness(1.2);
  }
}
</style>

<!-- Styles globaux appliqués dynamiquement -->
<style>
/* Import de la police rétro */
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

/* Styles appliqués au body quand le composant est monté */
body.crt-active-font {
  font-family: 'Press Start 2P', monospace;
  line-height: 1.6;
}

/* Reset complet pour le mode plein écran */
body.crt-active-fullscreen,
#app.crt-active-fullscreen {
  margin: 0 !important;
  padding: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  overflow: hidden !important;
  position: relative;
}

/* Effet Scanlines et Vignette pour l'effet CRT */
#app.crt-active-scanlines::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  pointer-events: none;
  z-index: 9998;

  /* Lignes de balayage horizontales */
  background: repeating-linear-gradient(
    0deg,
    rgba(0, 0, 0, 0.15),
    rgba(0, 0, 0, 0.15) 1px,
    transparent 1px,
    transparent 2px
  );

  /* Effet de scintillement */
  animation: scanlines-flicker 0.1s infinite;
}

/* Effet de vignette et aberration chromatique */
#app.crt-active-scanlines::after {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  pointer-events: none;
  z-index: 9999;

  /* Aberration chromatique subtile */
  background: linear-gradient(
    90deg,
    rgba(255, 0, 0, 0.03),
    rgba(0, 255, 0, 0.02),
    rgba(0, 0, 255, 0.03)
  );
  background-size: 3px 100%;

  /* Vignette sombre sur les bords */
  box-shadow:
    inset 0 0 100px rgba(0, 0, 0, 0.7),
    inset 0 0 200px rgba(0, 0, 0, 0.5);

  /* Légère courbure des bords (effet CRT) */
  border-radius: 2%;
}

/* Animation de scintillement pour les scanlines */
@keyframes scanlines-flicker {
  0% {
    opacity: 0.8;
  }
  50% {
    opacity: 0.85;
  }
  100% {
    opacity: 0.8;
  }
}

/* Effet de lueur sur le fond */
body.crt-active-fullscreen {
  background: radial-gradient(ellipse at center, rgba(0, 40, 0, 0.3) 0%, rgba(0, 0, 0, 1) 100%);
}
</style>
