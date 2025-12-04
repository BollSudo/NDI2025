<script setup lang="ts">
import Typewriter from '@/components/Typewriter.vue'
</script>

<template>
  <main>
    <div>
      <Typewriter
        :texts="['Votre premier texte cinématique', 'Le jeu commence...', 'Prêt ?']"
        :typingSpeed="60"
        :eraseSpeed="30"
        :pauseDuration="2500"
      />
    </div>
  </main>
</template>
