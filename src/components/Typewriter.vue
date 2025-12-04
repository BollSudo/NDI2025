<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps<{
  texts: string[]
  typingSpeed?: number
  eraseSpeed?: number
  pauseDuration?: number
  loopCount?: number // Nouveau: nombre de boucles avant fin
}>()

const emit = defineEmits<{
  (e: 'animation-complete'): void
}>()

const displayedText = ref('')
const currentTextIndex = ref(0)
const charIndex = ref(0)
const isDeleting = ref(false)
const timeoutId = ref<NodeJS.Timeout | null>(null)
const loopCount = ref(0)
const maxLoops = props.loopCount || 1

onMounted(() => startAnimation())
onUnmounted(() => {
  if (timeoutId.value) clearTimeout(timeoutId.value)
})

const startAnimation = () => typeOrDelete()

const typeOrDelete = () => {
  const currentText = props.texts[currentTextIndex.value]

  if (!isDeleting.value) {
    if (charIndex.value <= currentText.length) {
      displayedText.value = currentText.slice(0, charIndex.value)
      charIndex.value++
      timeoutId.value = setTimeout(typeOrDelete, props.typingSpeed || 80)
    } else {
      timeoutId.value = setTimeout(() => {
        isDeleting.value = true
        typeOrDelete()
      }, props.pauseDuration || 2000)
      return
    }
  } else {
    if (charIndex.value > 0) {
      displayedText.value = currentText.slice(0, charIndex.value - 1)
      charIndex.value--
      timeoutId.value = setTimeout(typeOrDelete, props.eraseSpeed || 40)
    } else {
      currentTextIndex.value = (currentTextIndex.value + 1) % props.texts.length
      if (currentTextIndex.value === 0) {
        loopCount.value++
        if (loopCount.value >= maxLoops) {
          emit('animation-complete')
          return
        }
      }
      isDeleting.value = false
      timeoutId.value = setTimeout(typeOrDelete, 500)
    }
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br  text-white p-8">
    <div class="text-center max-w-4xl mx-auto">
      <h1 class="text-5xl md:text-7xl font-bold font-mono tracking-wide mb-8 leading-tight">
        {{ displayedText }}
        <span class="inline-block w-4 h-12 bg-gradient-to-r from-blue-400 to-purple-500 animate-pulse ml-1">|</span>
      </h1>
    </div>
  </div>
</template>

<style scoped>
</style>
