<script setup lang="ts">
import { ref } from 'vue'
import Typewriter from '@/components/Typewriter.vue'

const showNavbar = ref(false)

const onAnimationComplete = () => {
  showNavbar.value = true
}
</script>

<template>
  <main class="min-h-screen">
    <Typewriter
      v-if="!showNavbar"
      :texts="['Votre premier texte cinématique', 'Le jeu commence...', 'Prêt ?']"
      :typingSpeed="60"
      :eraseSpeed="30"
      :pauseDuration="2500"
      :loopCount="1"
      @animation-complete="onAnimationComplete"
    />

    <nav v-else>
    </nav>
  </main>
</template>
