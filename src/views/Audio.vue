<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import CircularNavbar from '@/components/CircularNavbar.vue'

const fileInput = ref<HTMLInputElement | null>(null);
const audioPlayer = ref<HTMLAudioElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
const pulseImage = ref<HTMLImageElement | null>(null);

const isPlaying = ref(false);
const fileName = ref('');
const visualizerMode = ref('circular');

let audioContext: AudioContext | null = null;
let analyser: AnalyserNode | null = null;
let source: MediaElementAudioSourceNode | null = null;
let ctx: CanvasRenderingContext2D | null = null;
let animationId: number | null = null;
let hue = 0;
let rotationAngle = 0;

onMounted(() => {
  if (canvas.value) {
    ctx = canvas.value.getContext('2d');
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;
  }

  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId);
  }
  window.removeEventListener('resize', handleResize);
});

function handleResize() {
  if (canvas.value) {
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;
  }
}

function handleFile(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file || !audioPlayer.value) return;

  fileName.value = file.name;
  const url = URL.createObjectURL(file);
  audioPlayer.value.src = url;
}

function togglePlay() {
  if (!audioPlayer.value?.src) {
    alert('Veuillez d\'abord sélectionner un fichier audio !');
    return;
  }

  if (isPlaying.value) {
    audioPlayer.value.pause();
  } else {
    audioPlayer.value.play();
  }
}

function onPlay() {
  isPlaying.value = true;

  if (!audioContext) {
    initAudioContext();
  }

  if (!animationId) {
    animate();
  }
}

function onPause() {
  isPlaying.value = false;
}

function initAudioContext() {
  if (!audioPlayer.value) return;

  audioContext = new (window.AudioContext || (window as any).webkitAudioContext)();
  analyser = audioContext.createAnalyser();
  source = audioContext.createMediaElementSource(audioPlayer.value);

  source.connect(analyser);
  analyser.connect(audioContext.destination);
  analyser.fftSize = 256;
}

function changeMode() {
  // Force un refresh de la visualisation
  hue = 0;
  rotationAngle = 0;
}

function animate() {
  if (!analyser || !ctx || !canvas.value) return;

  animationId = requestAnimationFrame(animate);

  const bufferLength = analyser.frequencyBinCount;
  const dataArray = new Uint8Array(bufferLength);
  analyser.getByteFrequencyData(dataArray);

  // Calculer la moyenne pour les effets
  const avg = dataArray.reduce((a, b) => a + b, 0) / bufferLength;
  const scale = 1 + avg / 120;

  // Animer l'image centrale
  if (pulseImage.value) {
    pulseImage.value.style.transform =
      `translate(-50%, -50%) scale(${scale}) rotate(${rotationAngle}deg)`;
  }

  // Effet de traînée
  ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);

  const centerX = canvas.value.width / 2;
  const centerY = canvas.value.height / 2;

  switch (visualizerMode.value) {
    case 'circular':
      drawCircular(dataArray, centerX, centerY);
      break;
    case 'bars':
      drawBars(dataArray);
      break;
    case 'wave':
      drawWave(dataArray, centerX, centerY);
      break;
    case 'spiral':
      drawSpiral(dataArray, centerX, centerY);
      break;
  }
  if (isPlaying.value) {
    rotationAngle += 0.5;
  }
  hue = (hue + 1) % 360;
}

function drawCircular(dataArray: Uint8Array, centerX: number, centerY: number) {
  if (!ctx) return;

  const radius = 90;
  const bufferLength = dataArray.length;

  for (let i = 0; i < bufferLength; i++) {
    const angle = (i / bufferLength) * Math.PI * 2;
    const barHeight = (dataArray[i] / 255) * 400;

    const x1 = centerX + Math.cos(angle) * radius;
    const y1 = centerY + Math.sin(angle) * radius;
    const x2 = centerX + Math.cos(angle) * (radius + barHeight);
    const y2 = centerY + Math.sin(angle) * (radius + barHeight);

    const gradient = ctx.createLinearGradient(x1, y1, x2, y2);
    gradient.addColorStop(0, `hsl(${hue + i * 2}, 100%, 50%)`);
    gradient.addColorStop(1, `hsl(${hue + i * 2 + 60}, 100%, 70%)`);

    ctx.strokeStyle = gradient;
    ctx.lineWidth = 5;
    ctx.lineCap = 'round';

    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.stroke();
  }
}

function drawBars(dataArray: Uint8Array) {
  if (!ctx || !canvas.value) return;

  const bufferLength = dataArray.length;
  const barWidth = canvas.value.width / bufferLength;

  for (let i = 0; i < bufferLength; i++) {
    const barHeight = (dataArray[i] / 255) * canvas.value.height * 0.8;

    const gradient = ctx.createLinearGradient(0, canvas.value.height, 0, canvas.value.height - barHeight);
    gradient.addColorStop(0, `hsl(${hue + i * 3}, 100%, 50%)`);
    gradient.addColorStop(1, `hsl(${hue + i * 3 + 40}, 100%, 70%)`);

    ctx.fillStyle = gradient;
    ctx.fillRect(i * barWidth, canvas.value.height - barHeight, barWidth - 2, barHeight);
  }
}

function drawWave(dataArray: Uint8Array, centerX: number, centerY: number) {
  if (!ctx) return;

  const bufferLength = dataArray.length;

  ctx.beginPath();
  ctx.lineWidth = 4;

  for (let i = 0; i < bufferLength; i++) {
    const x = (i / bufferLength) * canvas.value!.width;
    const y = centerY + (dataArray[i] / 255) * 200 * Math.sin(i * 0.1 + hue * 0.05);

    if (i === 0) {
      ctx.moveTo(x, y);
    } else {
      ctx.lineTo(x, y);
    }
  }

  ctx.strokeStyle = `hsl(${hue}, 100%, 60%)`;
  ctx.stroke();

  // Onde miroir
  ctx.beginPath();
  for (let i = 0; i < bufferLength; i++) {
    const x = (i / bufferLength) * canvas.value!.width;
    const y = centerY - (dataArray[i] / 255) * 200 * Math.sin(i * 0.1 + hue * 0.05);

    if (i === 0) {
      ctx.moveTo(x, y);
    } else {
      ctx.lineTo(x, y);
    }
  }

  ctx.strokeStyle = `hsl(${hue + 180}, 100%, 60%)`;
  ctx.stroke();
}

function drawSpiral(dataArray: Uint8Array, centerX: number, centerY: number) {
  if (!ctx) return;

  const bufferLength = dataArray.length;

  for (let i = 0; i < bufferLength; i++) {
    const angle = (i / bufferLength) * Math.PI * 8 + hue * 0.01;
    const distance = 50 + i * 2;
    const size = (dataArray[i] / 255) * 30;

    const x = centerX + Math.cos(angle) * distance;
    const y = centerY + Math.sin(angle) * distance;

    ctx.fillStyle = `hsl(${hue + i * 2}, 100%, 60%)`;
    ctx.beginPath();
    ctx.arc(x, y, size, 0, Math.PI * 2);
    ctx.fill();
  }
}
</script>

<template>
  <circular-navbar />
  <div class="audio-container">
    <div class="header">
      <h1 class="title">Visualiseur Audio Rétro</h1>
      <p class="subtitle">{{ fileName || 'Sélectionnez votre musique préférée' }}</p>
    </div>

    <div class="controls">
      <label for="file-input" class="file-label">
        📁 Choisir un fichier
      </label>
      <input
        id="file-input"
        type="file"
        accept="audio/*"
        ref="fileInput"
        @change="handleFile"
        style="display: none;"
      >

      <audio
        ref="audioPlayer"
        @play="onPlay"
        @pause="onPause"
        @ended="onPause"
      ></audio>

      <button @click="togglePlay" class="play-btn" :class="{ playing: isPlaying }">
        {{ isPlaying ? '⏸️ Pause' : '▶️ Play' }}
      </button>

      <div class="visualizer-select">
        <label>Style: </label>
        <select v-model="visualizerMode" @change="changeMode">
          <option value="circular">Cercle Rétro</option>
          <option value="bars">Barres Classiques</option>
          <option value="wave">Onde Psychédélique</option>
          <option value="spiral">Spirale Cosmique</option>
        </select>
      </div>
    </div>

    <div class="visualizer-wrapper">
      <canvas ref="canvas" class="canvas"></canvas>
      <img
        ref="pulseImage"
        class="pulse-img"
        src="/tux.png"
        alt="music visual"
      />
    </div>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.audio-container {
  min-height: 100vh;
  color: white;
  font-family: 'Courier New', monospace;
  position: relative;
  overflow: hidden;
}

.header {
  text-align: center;
  padding: 30px 20px;
  position: relative;
  z-index: 10;
}

.title {
  font-size: 2.5rem;
  text-shadow: 0 0 20px #ff00ff, 0 0 40px #00ffff;
  animation: glow 2s ease-in-out infinite alternate;
}

.subtitle {
  margin-top: 10px;
  font-size: 1.2rem;
  color: #00ffff;
}

@keyframes glow {
  from {
    text-shadow: 0 0 20px #ff00ff, 0 0 40px #00ffff;
  }
  to {
    text-shadow: 0 0 30px #00ffff, 0 0 60px #ff00ff;
  }
}

.controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  padding: 20px;
  flex-wrap: wrap;
  position: relative;
  z-index: 10;
}

.file-label {
  background: linear-gradient(45deg, #ff00ff, #00ffff);
  padding: 12px 30px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: bold;
  transition: transform 0.2s;
  box-shadow: 0 5px 15px rgba(255, 0, 255, 0.4);
}

.file-label:hover {
  transform: scale(1.05);
}

.play-btn {
  background: linear-gradient(45deg, #00ff00, #00ffff);
  border: none;
  padding: 12px 40px;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  color: black;
  transition: all 0.3s;
  box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4);
}

.play-btn:hover {
  transform: scale(1.05);
}

.play-btn.playing {
  background: linear-gradient(45deg, #ff6600, #ff0000);
  animation: pulse 1s infinite;
}

@keyframes pulse {
  0%, 100% { box-shadow: 0 5px 15px rgba(255, 0, 0, 0.4); }
  50% { box-shadow: 0 5px 30px rgba(255, 0, 0, 0.8); }
}

.visualizer-select {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.1);
  padding: 10px 20px;
  border-radius: 25px;
  backdrop-filter: blur(10px);
}

.visualizer-select select {
  background-color: #181818;
  color: white;
  border: 2px solid #00ffff;
  padding: 8px 15px;
  border-radius: 15px;
  font-size: 1rem;
  cursor: pointer;
  outline: none;
}

.visualizer-wrapper {
  position: relative;
  width: 100%;
  height: calc(100vh - 200px);
}

.canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.pulse-img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 150px;
  height: 150px;
  border-radius: 50%;
  border: 5px solid rgba(255, 0, 255, 0.5);
  box-shadow: 0 0 30px #ff00ff, 0 0 60px #00ffff;
  transition: transform 0.1s ease-out;
  pointer-events: none;
  filter: drop-shadow(0 0 20px #ff00ff);
}

@media (max-width: 768px) {
  .title {
    font-size: 1.8rem;
  }

  .controls {
    flex-direction: column;
  }

  .pulse-img {
    width: 100px;
    height: 100px;
  }
}
</style>
