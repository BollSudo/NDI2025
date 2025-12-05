<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'
import ExploreNIRD from '@/components/ExploreNIRD.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const container = ref<HTMLDivElement>()
const showNextComponent = ref(false)
const isZooming = ref(false)

let scene: THREE.Scene
let camera: THREE.PerspectiveCamera
let renderer: THREE.WebGLRenderer
let controls: OrbitControls
const targetCameraPosition = new THREE.Vector3(1, 7, 9)
let shouldAnimateCamera = false // Nouveau flag

onMounted(() => {
  if (!container.value) return

  scene = new THREE.Scene()
  scene.background = new THREE.Color('#000000')

  camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000)
  camera.position.set(1, 2, 9)
  camera.lookAt(0, 0, 0)

  renderer = new THREE.WebGLRenderer({ antialias: true })
  renderer.setSize(window.innerWidth, window.innerHeight)
  container.value.appendChild(renderer.domElement)

  const ambientLight = new THREE.AmbientLight(0xffffff, 0.1)
  scene.add(ambientLight)

  const directionalLight = new THREE.DirectionalLight(0xffffff, 1)
  directionalLight.position.set(5, 5, 5)
  scene.add(directionalLight)

  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.05
  controls.enableZoom = false

  const maxAngle = (18 * Math.PI) / 180
  controls.minAzimuthAngle = -maxAngle
  controls.maxAzimuthAngle = maxAngle
  const verticalLimit = (90 * Math.PI) / 180
  controls.minPolarAngle = Math.PI / 2 - verticalLimit
  controls.maxPolarAngle = Math.PI / 2 - 0.1

  const loader = new GLTFLoader()
  loader.load(
    '/assets/models/computers.glb',
    (gltf) => {
      scene.add(gltf.scene)
    },
    () => {},
    (error) => {
      console.error('Error loading model:', error)
    },
  )

  animate()
  window.addEventListener('resize', onResize)
})

function exploreFurther() {
  if (isZooming.value) return

  isZooming.value = true
  shouldAnimateCamera = true

  controls.enabled = false
  targetCameraPosition.set(1, 2, 3)

  setTimeout(() => {
    shouldAnimateCamera = false
    showNextComponent.value = true
  }, 1500)
}

function animate() {
  requestAnimationFrame(animate)

  // Animer la caméra SEULEMENT pendant le zoom
  if (shouldAnimateCamera) {
    camera.position.lerp(targetCameraPosition, 0.08)
  }

  controls.update()
  renderer.render(scene, camera)
}

const navigateTo = (route: string) => {
  router.push(route)
}

function onResize() {
  camera.aspect = window.innerWidth / window.innerHeight
  camera.updateProjectionMatrix()
  renderer.setSize(window.innerWidth, window.innerHeight)
}

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  renderer.dispose()
})
</script>

<template>
  <div class="relative bg-black w-full h-screen">
    <div v-show="!showNextComponent" ref="container" class="w-full h-full"></div>

    <button
      v-if="!showNextComponent"
      @click="exploreFurther"
      :disabled="isZooming"
      class="absolute bottom-12 left-1/2 transform -translate-x-1/2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-full font-bold text-lg transition-all shadow-2xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed z-10"
    >
      <span v-if="!isZooming">Explorer</span>
      <span v-else class="flex items-center gap-2">
        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
            fill="none"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        Chargement...
      </span>
    </button>

    <transition
      enter-active-class="transition-opacity duration-500"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
    >
      <div v-if="showNextComponent" class="w-full h-full overflow-x-hidden">
        <ExploreNIRD />
      </div>
    </transition>
  </div>
  <div class="fixed right-0 bottom-0"><button class="hover:underline" @click="navigateTo('legals')">Mentions Légales</button></div>
</template>
