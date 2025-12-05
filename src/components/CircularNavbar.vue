<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isExpanded = ref(false)
const isDragging = ref(false)

const getInitialPosition = () => {
  return router.currentRoute.value.path === '/'
    ? { x: 50, y: 50 } // Centre pour la home
    : { x: 90, y: 10 } // Haut droite pour les autres pages
}

const position = ref(getInitialPosition())
const dragStart = ref({ x: 0, y: 0 })
const initialPosition = ref(getInitialPosition())

const navItems = [
  { id: 1, label: 'Accueil', route: '/', icon: '🏠' },
  { id: 2, label: 'Cours', route: '/cours', icon: '📖' },
  { id: 3, label: 'Quizz', route: '/quizz', icon: '📊' },
  { id: 4, label: 'Chatbot', route: '/chatbot', icon: '👥' },
  { id: 5, label: 'Audio', route: '/audio', icon: '🎧' },
  { id: 6, label: 'Carte des Talents', route: '/carte', icon: '👤' },
  { id: 7,label: 'Random', route: '/random', icon: '💎' },
]

const toggleMenu = () => {
  if (!isExpanded.value) {
    initialPosition.value = { ...position.value }
    position.value = { x: 50, y: 50 }
  } else {
    position.value = { ...initialPosition.value }
  }
  isExpanded.value = !isExpanded.value
}

const navigateTo = (route: string) => {
  router.push(route)
  isExpanded.value = false
  setTimeout(() => {
    const newPos = route === '/' ? { x: 50, y: 50 } : { x: 90, y: 10 }
    position.value = newPos
    initialPosition.value = newPos
  }, 400)
}

const startDrag = (e: MouseEvent | TouchEvent) => {
  if (isExpanded.value) return

  isDragging.value = true

  const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX
  const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY

  dragStart.value = {
    x: clientX - (position.value.x * window.innerWidth) / 100,
    y: clientY - (position.value.y * window.innerHeight) / 100,
  }
}

const onDrag = (e: MouseEvent | TouchEvent) => {
  if (!isDragging.value || isExpanded.value) return

  e.preventDefault()

  const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX
  const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY

  const newX = ((clientX - dragStart.value.x) / window.innerWidth) * 100
  const newY = ((clientY - dragStart.value.y) / window.innerHeight) * 100

  position.value = {
    x: Math.max(5, Math.min(95, newX)),
    y: Math.max(5, Math.min(95, newY)),
  }

  initialPosition.value = { ...position.value }
}

const stopDrag = () => {
  isDragging.value = false
}

onMounted(() => {
  document.addEventListener('mousemove', onDrag)
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchmove', onDrag, { passive: false })
  document.addEventListener('touchend', stopDrag)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('touchend', stopDrag)
})

const getRadius = () => {
  if (typeof window === 'undefined') return 140
  const width = window.innerWidth
  if (width < 480) return 100
  if (width < 768) return 120
  return 140
}

const getItemStyle = (index: number, total: number) => {
  const angle = (index * 360) / total - 90
  const r = getRadius()
  const x = Math.cos((angle * Math.PI) / 180) * r
  const y = Math.sin((angle * Math.PI) / 180) * r

  return {
    '--delay': `${index * 0.05}s`,
    '--x': `${x}px`,
    '--y': `${y}px`,
    transform: isExpanded.value ? `translate(${x}px, ${y}px) scale(1)` : 'translate(0, 0) scale(0)',
    opacity: isExpanded.value ? 1 : 0,
  }
}

const containerStyle = computed(() => ({
  left: `${position.value.x}%`,
  top: `${position.value.y}%`,
  transform: 'translate(-50%, -50%)',
  transition:
    isExpanded.value || isDragging.value
      ? 'none'
      : 'all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)',
}))
</script>

<template>
  <div class="fixed inset-0 pointer-events-none z-50">
    <div
      class="circular-navbar-container pointer-events-auto"
      :style="containerStyle"
      :class="{ dragging: isDragging, centered: isExpanded }"
    >
      <transition name="fade">
        <div
          v-if="isExpanded"
          @click="toggleMenu"
          class="fixed inset-0 bg-black/70 backdrop-blur-sm pointer-events-auto navbar-overlay"
        ></div>
      </transition>

      <div class="orbs-wrapper">
        <button
          @mousedown="startDrag"
          @touchstart="startDrag"
          @click.stop="toggleMenu"
          class="central-orb"
          :class="{ expanded: isExpanded, dragging: isDragging }"
        >
          <div class="orb-inner">
            <span class="central-icon" :class="{ 'rotate-90': isExpanded }">
              {{ isExpanded ? '✕' : '⚡' }}
            </span>
          </div>
          <div class="orb-glow"></div>
          <div class="orb-pulse" v-if="!isExpanded && !isDragging"></div>
        </button>

        <button
          v-for="(item, index) in navItems"
          :key="item.id"
          v-show="isExpanded"
          @click="navigateTo(item.route)"
          class="nav-orb"
          :style="getItemStyle(index, navItems.length)"
        >
          <div class="orb-inner-small">
            <span class="nav-icon">{{ item.icon }}</span>
            <span class="nav-label">{{ item.label }}</span>
          </div>
          <div class="orb-glow-small"></div>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.circular-navbar-container {
  position: fixed;
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  user-select: none;
  -webkit-user-select: none;
}

.circular-navbar-container.dragging {
  cursor: grabbing;
}

.circular-navbar-container.centered {
  left: 50% !important;
  top: 50% !important;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important;
}

.navbar-overlay {
  z-index: -1;
}

.orbs-wrapper {
  position: relative;
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.central-orb {
  position: absolute;
  top: 64%;
  left: 64%;
  transform: translate(-50%, -50%);
  width: 90px;
  height: 90px;
  background: radial-gradient(circle at 30% 30%, #00ff00, #00aa00);
  border: 3px solid #00ff00;
  border-radius: 50%;
  cursor: grab;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  z-index: 100;
  box-shadow:
    0 0 20px rgba(0, 255, 0, 0.6),
    0 0 40px rgba(0, 255, 0, 0.4),
    inset 0 0 20px rgba(0, 255, 0, 0.3);
}

.central-orb.dragging {
  cursor: grabbing;
  transform: translate(-50%, -50%) scale(1.05);
}

.central-orb:hover:not(.dragging) {
  transform: translate(-50%, -50%) scale(1.1);
  box-shadow:
    0 0 30px rgba(0, 255, 0, 0.8),
    0 0 60px rgba(0, 255, 0, 0.5),
    inset 0 0 25px rgba(0, 255, 0, 0.5);
}

.central-orb.expanded {
  background: radial-gradient(circle at 30% 30%, #ff3333, #cc0000);
  border-color: #ff3333;
  cursor: pointer;
  box-shadow:
    0 0 20px rgba(255, 51, 51, 0.6),
    0 0 40px rgba(255, 51, 51, 0.4),
    inset 0 0 20px rgba(255, 51, 51, 0.3);
}

.orb-inner {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: 'Press Start 2P', monospace;
  text-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
  pointer-events: none;
}

.central-icon {
  font-size: 2rem;
  transition: transform 0.3s ease;
  display: block;
}


.orb-glow {
  position: absolute;
  inset: -8px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(0, 255, 0, 0.4), transparent 70%);
  animation: glow-pulse 2s ease-in-out infinite;
  pointer-events: none;
  z-index: -1;
}

.central-orb.expanded .orb-glow {
  background: radial-gradient(circle, rgba(255, 51, 51, 0.4), transparent 70%);
}

.orb-pulse {
  position: absolute;
  inset: -10px;
  border-radius: 50%;
  border: 2px solid #00ff00;
  animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
  pointer-events: none;
  z-index: -1;
}

@keyframes pulse-ring {
  0% {
    transform: scale(0.9);
    opacity: 1;
  }
  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

@keyframes glow-pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.nav-orb {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100px;
  height: 100px;
  margin-top: -37.5px;
  margin-left: -37.5px;
  background: radial-gradient(circle at 30% 30%, #00ddff, #0088cc);
  border: 2px solid #00ddff;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  transition-delay: var(--delay);
  z-index: 50;
  box-shadow:
    0 0 15px rgba(0, 221, 255, 0.6),
    0 0 30px rgba(0, 221, 255, 0.3),
    inset 0 0 15px rgba(0, 221, 255, 0.3);
  pointer-events: auto;
}

.nav-orb:hover {
  transform: translate(var(--x), var(--y)) scale(1.2) !important;
  box-shadow:
    0 0 25px rgba(0, 221, 255, 0.8),
    0 0 50px rgba(0, 221, 255, 0.5),
    inset 0 0 20px rgba(0, 221, 255, 0.5);
  z-index: 60;
}

.orb-inner-small {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: 'Press Start 2P', monospace;
  text-shadow: 0 0 8px rgba(0, 0, 0, 0.8);
  padding: 8px;
  gap: 4px;
  pointer-events: none;
}

.nav-icon {
  font-size: 1.5rem;
  display: block;
}

.nav-label {
  font-size: 0.5rem;
  text-align: center;
  line-height: 1.2;
  max-width: 100%;
  word-break: break-word;
}

.orb-glow-small {
  position: absolute;
  inset: -5px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(0, 221, 255, 0.3), transparent 70%);
  pointer-events: none;
  z-index: -1;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .central-orb {
    width: 80px;
    height: 80px;
  }

  .central-icon {
    font-size: 1.75rem;
  }

  .nav-orb {
    width: 65px;
    height: 65px;
    margin-top: -32.5px;
    margin-left: -32.5px;
  }

  .nav-icon {
    font-size: 1.25rem;
  }

  .nav-label {
    font-size: 0.45rem;
  }
}

@media (max-width: 480px) {
  .central-orb {
    width: 70px;
    height: 70px;
  }

  .central-icon {
    font-size: 1.5rem;
  }

  .nav-orb {
    width: 55px;
    height: 55px;
    margin-top: -27.5px;
    margin-left: -27.5px;
  }

  .nav-icon {
    font-size: 1rem;
  }

  .nav-label {
    font-size: 0.4rem;
    line-height: 1.1;
  }

  .orb-inner-small {
    padding: 6px;
    gap: 2px;
  }
}

@media (max-width: 360px) {
  .central-orb {
    width: 60px;
    height: 60px;
  }

  .central-icon {
    font-size: 1.25rem;
  }

  .nav-orb {
    width: 48px;
    height: 48px;
    margin-top: -24px;
    margin-left: -24px;
  }

  .nav-icon {
    font-size: 0.875rem;
  }

  .nav-label {
    font-size: 0.35rem;
  }
}
</style>
