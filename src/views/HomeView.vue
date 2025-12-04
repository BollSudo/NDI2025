<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router'
import Typewriter from '@/components/Typewriter.vue'; // Notre composant Typewriter

const router = useRouter()

const showNavbar = ref(false)

const onAnimationComplete = () => {
  showNavbar.value = true
}
// --- Configuration des Diapositives (Slides) ---
const slides = [
  "// BOOT SEQUENCE INITIEE...\n\nANNEE 2025. Le systeme scolaire est envahi par les dependances numeriques...\n\nPerte de controle. Obscurite. Epuisement des ressources.",
  "UNE ANCIENNE PROPHETIE parle de la demarche NIRD (Numerique Inclusif, Responsable et Durable).",
  "NIRD est la cle. Elle promet autonomie, durabilite et equite. Mais le chemin est long. Il faut une carte. Il faut un GUIDE.",
  "BIENVENUE, HEROS. Ta mission est d'aider les etablissements a PASSER AU NIVEAU SUPERIEUR.\n\nEs-tu pret a debute ta quete ?"
];
const totalSlides = slides.length;

// --- États Réactifs ---
const currentSlideIndex = ref(0);
const isTyping = ref(true); // Démarre en mode frappe (Typewriter)
const isFinished = ref(false); // Vrai si toute la cinématique est terminée

// --- Computed Properties pour l'UI ---

// Le texte actuel à passer au composant Typewriter
const currentTextForTypewriter = computed(() => slides[currentSlideIndex.value]);

// Calcul de la progression pour la barre HUD
const progressPercentage = computed(() => {
  return ((currentSlideIndex.value + 1) / totalSlides) * 100;
});

// Message d'instruction
const instructionMessage = computed(() => {
  return currentSlideIndex.value < totalSlides - 1
    ? "> APPUYER SUR [ESPACE] POUR CONTINUER <"
    : "> APPUYER SUR [ESPACE] POUR ENTRER DANS L'APPLICATION <";
});

// Affichage/Masquage de l'instruction (clignote uniquement quand la frappe est finie)
const showInstruction = computed(() => {
  return !isTyping.value && !isFinished.value;
});

// --- Logique de Progression ---

// Appelé par le composant Typewriter lorsqu'il a fini de taper
const handleTypingFinished = () => {
  isTyping.value = false;
};

// Passe à la diapositive suivante ou termine
const nextSlide = () => {
  // Si la frappe est encore en cours, on ignore le clic/la touche.
  if (isTyping.value) {
    return;
  }

  // Si c'est la dernière diapositive, on termine l'intro.
  if (currentSlideIndex.value >= totalSlides - 1) {
    isFinished.value = true;
    // Ici, vous pouvez ajouter la logique pour afficher le contenu principal de la HomeView
    onAnimationComplete()
    return;
  }

  // Sinon, on passe à la diapositive suivante
  currentSlideIndex.value++;
  isTyping.value = true; // Redémarre l'état de frappe pour la nouvelle diapositive
};

// --- Gestionnaire d'Événements Clavier ---

const handleKeyPress = (event: KeyboardEvent) => {
  if (event.code === 'Space') {
    event.preventDefault(); // Empêche le scroll si possible
    nextSlide();
  }
};

// --- Gestion des Styles Globaux (Isolation CRT) ---

// Classes utilisées pour cibler le body et #app dans le bloc <style> non scoped
const CRT_FONT_CLASS = 'crt-active-font';
const CRT_SCANLINES_CLASS = 'crt-active-scanlines';
// NOUVELLE CLASSE POUR LE PLEIN ÉCRAN
const CRT_FULLSCREEN_CLASS = 'crt-active-fullscreen';


onMounted(() => {
  window.addEventListener('keydown', handleKeyPress);
  // AJOUT: Appliquer les styles globaux du body/app au montage du composant
  document.body.classList.add(CRT_FONT_CLASS, CRT_FULLSCREEN_CLASS); // Ajout de la classe fullscreen
  const appElement = document.getElementById('app');
  if (appElement) {
    appElement.classList.add(CRT_SCANLINES_CLASS, CRT_FULLSCREEN_CLASS); // Ajout de la classe fullscreen
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyPress);
  // AJOUT: Retirer les styles globaux au démontage
  document.body.classList.remove(CRT_FONT_CLASS, CRT_FULLSCREEN_CLASS); // Retrait de la classe fullscreen
  const appElement = document.getElementById('app');
  if (appElement) {
    appElement.classList.remove(CRT_SCANLINES_CLASS, CRT_FULLSCREEN_CLASS); // Retrait de la classe fullscreen
  }
});
</script>

<template>
  <main>
  <!-- Le conteneur parent prend la taille de l'écran et centre son contenu. -->
  <div v-if="!isFinished" class="min-h-screen w-full w-screen bg-black  text-gray-200 flex justify-center items-center">

    <!-- Contenu cinématique visible tant que isFinished est faux -->
    <!-- MODIFICATION CLÉ : Retrait de mx-auto et ajout de ml-12 mr-6 pour décaler vers la droite. -->
    <div  class="crt-screen  w-4/5 relative max-h-screen p-4 md:p-8 shadow-green-glow min-h-[50vh] flex flex-col justify-between rounded-lg bg-black/90 transition-all duration-700">

      <!-- Section du contenu principal (l'histoire) -->
      <div id="intro-text" class="text-sm md:text-base lg:text-lg text-center leading-relaxed transition-opacity duration-500">
        <!-- Notre composant machine à écrire -->
        <Typewriter
          v-if="!showNavbar"
          :text="currentTextForTypewriter"
          :typing-speed="40"
          @finished="handleTypingFinished"
        />

      </div>


      <!-- Faux HUD Rétro Gaming (en bas) -->
      <!-- J'ai assuré que le HUD utilise des petites polices pour rester compact -->
      <div class="mt-8 text-xs md:text-sm pt-4 border-t border-green-700/50">
        <!-- Jauge de Progression (Loading Bar) -->
        <div class="loading-bar-container w-full h-4 mb-2 rounded-sm overflow-hidden border-2 border-green-500 bg-gray-900">
          <div
            id="progress-bar"
            class="loading-bar-fill h-full bg-green-500 transition-all duration-300"
            :style="{ width: `${progressPercentage}%` }"
          />
        </div>

        <!-- Compteurs et Version OS -->
        <div class="flex justify-between font-mono text-green-400">
          <span id="slide-counter">{{ `NIRD SEQUENCE ${currentSlideIndex + 1}/${totalSlides}` }}</span>
          <span>NIRD OS v1.0.0 (C) 2025</span>
        </div>

        <!-- Instruction (Clignotante) -->
        <div
          id="instruction-message"
          :class="['mt-4 text-center text-yellow-300 font-bold transition-opacity', { 'animate-pulse-slow': showInstruction, 'opacity-0': !showInstruction }]"
        >
          {{ instructionMessage }}
        </div>
      </div>
    </div>

    <!-- Contenu de l'application (Visible après la cinématique) -->

  </div>
    <nav v-else>
      <p>poznepfpeinfpezfpof,fpejoêkôaekdôakd</p>
    </nav>
  </main>
</template>

<style scoped>
/* Styles SCOPED: s'appliquent uniquement aux éléments de ce composant */

/* Ombre portée personnalisée pour le glow du conteneur (intérieur et extérieur) */
.shadow-green-glow {
  box-shadow: 0 0 15px rgba(0, 255, 0, 0.5), inset 0 0 10px rgba(0, 255, 0, 0.3);
}

/* Effet de glow pour le texte */
#intro-text {
  text-shadow: 0 0 4px #0f0, 0 0 8px #0f0;
}

/* Animation de clignotement plus lente pour les instructions */
.animate-pulse-slow {
  animation: slow-pulse 1.5s infinite;
}

@keyframes slow-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.2; }
}
</style>

<!-- Styles NON SCOPED: Appliqués au body et #app pour l'effet plein écran -->
<style>
/* 1. Import de la police - on la place ici pour qu'elle ne soit chargée que si HomeView est monté */
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

/* 2. Styles du body/app appliqués UNIQUEMENT quand le composant HomeView est monté (via JS) */
body.crt-active-font {
  font-family: 'Press Start 2P', monospace;
}

/* 4. *** AJOUT CLÉ : Désactiver les marges et paddings globaux potentiellement hérités *** */
body.crt-active-fullscreen,
#app.crt-active-fullscreen {
  margin: 0 !important; /* Force la suppression de marges externes */
  padding: 0 !important; /* Force la suppression de paddings externes */

}


/* 3. Effet Scanlines et Vignette appliqué au conteneur principal #app */
#app.crt-active-scanlines::after {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 9999;

  /* Simule les lignes du tube cathodique */
  background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
  linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
  background-size: 100% 4px, 3px 100%;
  opacity: 0.6;

  /* Effet Vignette (assombrissement des bords) */
  box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.9);
}
</style>
