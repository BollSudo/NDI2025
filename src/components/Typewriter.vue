<script>
export default {
  name: 'Typewriter',
  props: {
    texts: {
      type: Array,
      required: true,
      default: () => ['Bienvenue dans le jeu', 'Préparez-vous à jouer', 'Appuyez pour commencer']
    },
    typingSpeed: { type: Number, default: 80 }, // ms par caractère
    eraseSpeed: { type: Number, default: 40 },
    pauseDuration: { type: Number, default: 2000 } // ms entre textes
  },
  data() {
    return {
      displayedText: '',
      currentTextIndex: 0,
      charIndex: 0,
      isDeleting: false,
      timeoutId: null
    }
  },
  mounted() {
    this.startAnimation()
  },
  beforeUnmount() {
    if (this.timeoutId) clearTimeout(this.timeoutId)
  },
  methods: {
    startAnimation() {
      this.typeOrDelete()
    },
    typeOrDelete() {
      const currentText = this.texts[this.currentTextIndex]

      if (!this.isDeleting) {
        // Tape le texte
        if (this.charIndex <= currentText.length) {
          this.displayedText = currentText.slice(0, this.charIndex)
          this.charIndex++
          this.timeoutId = setTimeout(() => this.typeOrDelete(), this.typingSpeed)
        } else {
          // Pause avant effacement
          this.timeoutId = setTimeout(() => {
            this.isDeleting = true
            this.typeOrDelete()
          }, this.pauseDuration)
          return
        }
      } else {
        // Efface le texte
        if (this.charIndex > 0) {
          this.displayedText = currentText.slice(0, this.charIndex - 1)
          this.charIndex--
          this.timeoutId = setTimeout(() => this.typeOrDelete(), this.eraseSpeed)
        } else {
          // Texte suivant
          this.currentTextIndex = (this.currentTextIndex + 1) % this.texts.length
          this.isDeleting = false
          this.timeoutId = setTimeout(() => this.typeOrDelete(), 500)
        }
      }
    }
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-black text-white p-8">
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
