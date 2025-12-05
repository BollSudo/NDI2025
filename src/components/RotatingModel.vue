<template>
  <div ref="container" class="w-full h-full"></div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'


interface Props {
  modelPath: string
  rotationSpeed?: number
  autoRotate?: boolean
  cameraPosition?: [number, number, number]
  ambientLightIntensity?: number
}

const props = withDefaults(defineProps<Props>(), {
  rotationSpeed: 0.01,
  autoRotate: true,
  cameraPosition: () => [0, 0, 4]
})

const container = ref<HTMLDivElement>()
let scene: THREE.Scene
let camera: THREE.PerspectiveCamera
let renderer: THREE.WebGLRenderer
let controls: OrbitControls
let model: THREE.Group
let animationFrameId: number

onMounted(() => {
  if (!container.value) return
  initScene()
  loadModel()
  animate()
  window.addEventListener('resize', onResize)
})

function initScene() {
  // Scene
  scene = new THREE.Scene()

  // Camera
  camera = new THREE.PerspectiveCamera(
    45,
    container.value!.clientWidth / container.value!.clientHeight,
    0.1,
    1000
  )
  camera.position.set(...props.cameraPosition)

  // Renderer
  renderer = new THREE.WebGLRenderer({
    antialias: true,
    alpha: true
  })
  renderer.setSize(container.value!.clientWidth, container.value!.clientHeight)
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap
  container.value!.appendChild(renderer.domElement)

  // Lights
  const ambientLight = new THREE.AmbientLight(0xffffff, props.ambientLightIntensity ?? 0.6)
  scene.add(ambientLight)

  const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8)
  directionalLight.position.set(5, 10, 5)
  directionalLight.castShadow = true
  directionalLight.shadow.mapSize.width = 2048
  directionalLight.shadow.mapSize.height = 2048
  scene.add(directionalLight)

  const hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.4)
  scene.add(hemisphereLight)

  // Controls
  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.05
  controls.enableZoom = false
  controls.autoRotate = props.autoRotate
  controls.autoRotateSpeed = 1
}

function loadModel() {
  const loader = new GLTFLoader()

  loader.load(
    props.modelPath,
    (gltf) => {
      model = gltf.scene

      // Center the model
      const box = new THREE.Box3().setFromObject(model)
      const center = box.getCenter(new THREE.Vector3())
      model.position.sub(center)

      // Enable shadows
      model.traverse((child) => {
        if ((child as THREE.Mesh).isMesh) {
          child.castShadow = true
          child.receiveShadow = true
        }
      })

      scene.add(model)
      console.log('Model loaded successfully')
    },
    (progress) => {
      const percentComplete = (progress.loaded / progress.total) * 100
      console.log(`Loading: ${percentComplete.toFixed(2)}%`)
    },
    (error) => {
      console.error('Error loading model:', error)
    }
  )
}

function animate() {
  animationFrameId = requestAnimationFrame(animate)

  // Rotate model if exists
  if (model && props.rotationSpeed > 0) {
    model.rotation.y += props.rotationSpeed
  }

  controls.update()
  renderer.render(scene, camera)
}

function onResize() {
  if (!container.value) return

  camera.aspect = container.value.clientWidth / container.value.clientHeight
  camera.updateProjectionMatrix()
  renderer.setSize(container.value.clientWidth, container.value.clientHeight)
}

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  cancelAnimationFrame(animationFrameId)
  renderer.dispose()
  controls.dispose()
})
</script>

<style scoped>
div {
  width: 100%;
  height: 100%;
}
</style>
