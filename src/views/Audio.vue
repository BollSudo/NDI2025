<template>
    <div class="audio">
      <h1>Une page magnifique pour voir vos musiques</h1>
      <div class="choice_audio">
        <input type="file" accept="audio/*" ref="fileInput" @change="handleFile">
        <audio ref="audioPlayer" @play="play" controls></audio>
        <button @click="play">Play</button>
        <img class="pulse-img" src="/tux.png" alt="music image" width="300" />

      </div>
      <canvas class="canva" ref="canvas"></canvas>
    </div>

</template>

<style>

.pulse-img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(1);
  width: 250px;
  height: 250px;
  object-fit: cover;
  border-radius: 50%; /* cercle */
  transition: transform 0.05s linear; /* animation douce */
  pointer-events: none;
}

.canva {
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

@media (min-width: 1024px) {
  .audio {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}

.choice_audio {
  margin: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const fileInput = ref<HTMLInputElement|null>(null);
const audioPlayer = ref<HTMLAudioElement|null>(null);
const canvas = ref<HTMLCanvasElement|null>(null);
const pulseImage = ref<HTMLImageElement|null>(null);

let audio = new Audio();

let analyser;
let context;
let source;
let ctx: CanvasRenderingContext2D | null = null;

onMounted(() => {
  if (canvas.value) {
    ctx = canvas.value.getContext('2d');
  }
});


function handleFile(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file || !audioPlayer.value) return;

  const url = URL.createObjectURL(file);
  audioPlayer.value.src = url;
}

function play() {
  // Amplitude.play();

  context = new AudioContext();

  source = context.createMediaElementSource(audioPlayer.value);
  analyser = context.createAnalyser(audio);

  source.connect(analyser);
  analyser.connect(context.destination);
  analyser.fftSize = 64;

  const bufferLength = analyser.frequencyBinCount;

  const data = new Uint8Array(bufferLength);

  const barWidth = canvas.value.width / bufferLength;
  let bareHeight;
  let x;
  const avg = data.reduce((a, b) => a + b, 0) / data.length;
  const scale = 1 + avg / 180;

  function update() {
    x = 0;

    ctx.clearRect(0,0,canvas.value.width,canvas.value.height);
    analyser.getByteFrequencyData(data);

    for (let i=0; i<bufferLength; i++) {
      bareHeight = data[i];
      ctx.fillStyle = 'white';
      ctx.fillRect(x, canvas.value.height - bareHeight, barWidth, canvas.value.width, bareHeight);
      x += barWidth;
      ctx.fill();
    }

    const avg = data.reduce((a, b) => a + b, 0) / data.length;
    const scale = 1 + avg / 180;

    if (pulseImage.value) {
      pulseImage.value.style.transform = `translate(-50%, -50%) scale(${scale})`;
    }

    // Ici tu peux changer ton oscillateur ou image

    requestAnimationFrame(update);
  }
  update();
}
</script>
