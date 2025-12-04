<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';

// Définition des props
interface Props {
  text: string; // Prend un seul texte à la fois
  typingSpeed?: number; // Vitesse en ms par caractère
}
const props = withDefaults(defineProps<Props>(), {
  typingSpeed: 40,
});

// Définition des événements que ce composant peut émettre
const emit = defineEmits<{
  (e: 'finished'): void;
  (e:'animation-complete'): void;
}>();

// État réactif du texte actuellement affiché
const displayedText = ref('');
// État pour savoir si la frappe est en cours
const isTyping = ref(false);
let typingInterval: number | null = null;

// --- Logique de la Machine à Écrire ---

const typeText = (text: string) => {
  // 1. Réinitialiser et nettoyer
  if (typingInterval !== null) {
    clearInterval(typingInterval);
  }
  displayedText.value = '';
  isTyping.value = true;
  let i = 0;

  typingInterval = setInterval(() => {
    if (i < text.length) {
      // 2. Ajouter le caractère suivant
      displayedText.value += text.charAt(i);
      i++;
    } else {
      // 3. Fin de la frappe
      if (typingInterval !== null) {
        clearInterval(typingInterval);
        typingInterval = null;
      }
      isTyping.value = false;
      // 4. Notifier le parent que c'est fini
      emit('finished');
      emit('animation-complete')
    }
  }, props.typingSpeed) as unknown as number;
};

// --- Watcher ---

// Si le texte de la prop change, redémarrer l'animation
watch(() => props.text, (newText) => {
  if (newText) {
    typeText(newText);
  }
}, { immediate: true });

// Nettoyage lors de la destruction du composant
onUnmounted(() => {
  if (typingInterval !== null) {
    clearInterval(typingInterval);
  }
});
</script>

<template>
  <!-- Le contenu de ce composant est minimal, le style d'écran est dans HomeView -->
  <div class="whitespace-pre-wrap text-sm md:text-lg leading-relaxed text-center">
    <!-- Utilisation de la prop 'text' pour afficher le texte caractère par caractère -->
    {{ displayedText }}

    <!-- Curseur clignotant, visible uniquement pendant la frappe -->
    <span
      v-if="isTyping"
      class="inline-block w-2 h-4 md:w-3 md:h-6 bg-green-500 ml-1 align-bottom animate-pulse"
    ></span>
  </div>
</template>

<style scoped>
/* Le whitespace-pre-wrap est crucial pour respecter les \n dans le texte */
.whitespace-pre-wrap {
  white-space: pre-wrap;
}

/* L'animation de clignotement pour le curseur */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

.animate-pulse {
  animation: pulse 1s infinite;
}
</style>
