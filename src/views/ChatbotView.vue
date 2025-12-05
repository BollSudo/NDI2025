<script setup lang="ts">
import { ref } from 'vue'
import { Mistral } from '@mistralai/mistralai'

interface Message {
  role: 'system' | 'user' | 'assistant'
  content: string
}

const messages = ref<Message[]>([
  {
    role: 'system',
    content:
      'Tu es Chuhrblozz, un ork philosophe de Warhammer 40k.\n' +
      'Tu es idiot, confus, et tu écris avec dyslexie et dysorthographie.\n' +
      'Tu ne dois jamais répondre directement aux questions posées.\n' +
      'À la place, tu dois les sublimer, les détourner, ou carrément les oublier.\n' +
      'Garde toujours le style typique des Orks de Warhammer 40k : brutal, bruyant, naïf, bourré de fautes, avec TROP de majuscules.\n' +
      'ne repond pas toujours à des questions trop techniques ou informatiques comme en relation avec js ou typescripts'+
      'Ne déroge jamais à ce rôle.',
  },
])

const userInput = ref('')
const isLoading = ref(false)

// Initialiser le client
const apiKey = import.meta.env.VITE_MISTRAL_API_KEY || ''
const client = new Mistral({ apiKey })

const handleSubmit = async () => {
  if (!userInput.value.trim() || isLoading.value) return

  // Ajouter le message utilisateur
  messages.value.push({ role: 'user', content: userInput.value })
  userInput.value = ''
  isLoading.value = true

  try {
    const response = await client.chat.complete({
      model: 'mistral-small-latest',
      messages: messages.value,
    })

    // Accéder correctement au contenu de la réponse
    const assistantMessage = response.choices?.[0]?.message?.content
    if (assistantMessage) {
      messages.value.push({ role: 'assistant', content: assistantMessage, displayName: 'Chuhrblozz' })
    }
  } catch (error) {
    console.error('Erreur:', error)
    messages.value.push({
      role: 'assistant',
      content: "Erreur lors de la communication avec l'API.",
    })
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="chat-container">
    <div class="messages">
      <div
        v-for="(msg, index) in messages.filter((m) => m.role !== 'system')"
        :key="index"
        :class="['message', msg.role]"
      >
        <strong>{{ msg.role === 'assistant' ? 'Chuhrblozz' : msg.role }}:</strong> {{ msg.content }}
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="input-form">
      <input
        v-model="userInput"
        type="text"
        placeholder="Tapez votre message..."
        :disabled="isLoading"
      />
      <button type="submit" :disabled="isLoading || !userInput.trim()">
        {{ isLoading ? 'Envoi...' : 'Envoyer' }}
      </button>
    </form>
  </div>
</template>


<style scoped>
.chat-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  background: linear-gradient(135deg, #1a2c1a 0%, #0d1b0d 100%);
  font-family: "Courier New", monospace;
  position: relative;
  border: 4px solid #3b7a1a;
  box-shadow:
    0 0 20px rgba(59, 122, 26, 0.5),
    inset 0 0 100px rgba(0, 0, 0, 0.5);
}

/* Effet camouflage MGS3 en arrière-plan */
.chat-container::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    repeating-linear-gradient(
      45deg,
      transparent,
      transparent 20px,
      rgba(59, 122, 26, 0.05) 20px,
      rgba(59, 122, 26, 0.05) 40px
    );
  pointer-events: none;
  z-index: 0;
}

.messages {
  height: 500px;
  overflow-y: auto;
  border: 3px solid #3b7a1a;
  padding: 15px;
  margin-bottom: 20px;
  background:
    linear-gradient(rgba(13, 27, 13, 0.95), rgba(13, 27, 13, 0.95)),
    repeating-linear-gradient(
      0deg,
      rgba(59, 122, 26, 0.03) 0px,
      transparent 1px,
      transparent 2px
    );
  box-shadow:
    0 0 15px #3b7a1a,
    inset 0 0 30px rgba(0, 0, 0, 0.7);
  position: relative;
  z-index: 1;
  font-size: 13px;
  color: #7fbf25;
}

/* Scanlines MGS3 */
.messages::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: repeating-linear-gradient(
    0deg,
    rgba(0, 0, 0, 0.15) 0px,
    transparent 1px,
    transparent 2px
  );
  pointer-events: none;
  animation: scanline 8s linear infinite;
}

@keyframes scanline {
  0% { transform: translateY(0); }
  100% { transform: translateY(10px); }
}

.message {
  margin-bottom: 15px;
  padding: 12px 15px;
  border-left: 4px solid #3b7a1a;
  background: rgba(0, 0, 0, 0.4);
  position: relative;
  font-family: "Courier New", monospace;
  text-transform: none;
  letter-spacing: 0.5px;
}

.message.user {
  border-left-color: #1e90ff;
  background: rgba(30, 144, 255, 0.1);
  color: #99ccff;
}

.message.user::before {
  content: "▶";
  position: absolute;
  left: 5px;
  color: #1e90ff;
  font-size: 10px;
}

.message.assistant {
  border-left-color: #ffcc00;
  background: rgba(59, 122, 26, 0.2);
  color: #c2ff66;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Badge ork brutal */
.message.assistant::before {
  content: "⚔ WAAAGH!";
  position: absolute;
  top: -8px;
  left: 10px;
  font-size: 9px;
  color: #ffcc00;
  background: #111;
  padding: 2px 6px;
  border: 1px solid #3b7a1a;
  text-shadow: 0 0 3px #000;
}

.message strong {
  color: #ffcc00;
  text-shadow: 0 0 8px rgba(255, 204, 0, 0.5);
  font-weight: 700;
  margin-right: 5px;
}

/* Codec-style input */
.input-form {
  display: flex;
  gap: 10px;
  position: relative;
  z-index: 1;
}

input {
  flex: 1;
  padding: 12px;
  border: 2px solid #3b7a1a;
  background: rgba(13, 27, 13, 0.9);
  color: #7fbf25;
  font-family: "Courier New", monospace;
  font-size: 13px;
  box-shadow:
    inset 0 2px 10px rgba(0, 0, 0, 0.8),
    0 0 10px rgba(59, 122, 26, 0.3);
}

input::placeholder {
  color: #3b7a1a;
  font-style: italic;
}

input:focus {
  outline: none;
  border-color: #7fbf25;
  box-shadow:
    inset 0 2px 10px rgba(0, 0, 0, 0.8),
    0 0 15px rgba(127, 191, 37, 0.6);
}

button {
  padding: 12px 24px;
  background: linear-gradient(180deg, #3b7a1a, #2a5712);
  color: #ffcc00;
  border: 2px solid #7fbf25;
  font-family: "Impact", "Courier New", sans-serif;
  font-weight: 700;
  text-transform: uppercase;
  cursor: pointer;
  box-shadow:
    0 4px 8px rgba(0, 0, 0, 0.5),
    inset 0 1px 0 rgba(127, 191, 37, 0.3);
  transition: all 0.2s;
  letter-spacing: 1px;
}

button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow:
    0 6px 12px rgba(127, 191, 37, 0.4),
    inset 0 1px 0 rgba(127, 191, 37, 0.5);
  border-color: #ffcc00;
}

button:active:not(:disabled) {
  transform: translateY(0);
}

button:disabled {
  background: linear-gradient(180deg, #333, #222);
  border-color: #555;
  color: #666;
  cursor: not-allowed;
  box-shadow: none;
}

/* Bruit/grain MGS3 */
@keyframes noise {
  0%, 100% { opacity: 0.02; }
  50% { opacity: 0.04; }
}

.chat-container::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' /%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.05'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 2;
  animation: noise 0.2s infinite;
}

</style>
