<script setup lang="ts">
import Nav from '@/components/SkillCard/Nav.vue'
import { computed, reactive, ref } from 'vue'
import axios from 'axios'
import router from '@/router'

const submitting = ref(false)
const showPassword = ref(false)
const serverError = ref(null)
const isValid = computed(() => {
  return (
    form.email &&
    form.password
  )
})

interface FormData {
  email: string
  password: string
}

const form = reactive<FormData>({
  email: '',
  password: '',
})

async function submit() {
  submitting.value = true
  try {
    const payload = {
      email: form.email,
      password: form.password,
    }
    await axios.post(`${import.meta.env.VITE_BACKEND_URL}/api/auth`, payload, {
      headers: { 'Content-Type': 'application/json' },
    })
    setTimeout(() => router.push('/skill_card'), 1500)
  } catch (err: any) {
    console.log('Erreur complète:', err.response?.data) // Debug

    // ✅ MESSAGE GÉNÉRAL (ex: "Cet email est déjà utilisé.")
    if (err.response?.data?.message) {
      serverError.value = err.response.data.message
    }
    // ✅ VIOLATIONS VALIDATION (ex: email, password...)
    else if (err.response?.data?.violations) {
      const violations = err.response.data.violations
      serverError.value = violations.map((v: any) => `${v.propertyPath}: ${v.message}`).join('; ')
    }
    // ✅ ERREUR UNIQUE (ex: ancien format)
    else if (err.response?.data?.error) {
      serverError.value = err.response.data.error
    }
    // ✅ ERREUR DÉTAILLÉE (ex: 500 server error)
    else if (err.response?.data) {
      serverError.value = err.response.data
    }
    // ✅ ERREUR RÉSEAU
    else {
      serverError.value = err.message || 'Erreur réseau inconnue'
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <Nav />
  <div class="flex items-center justify-center w-full">
    <form class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-10">
      <h1 class="text-3xl font-semibold text-green-700 mb-10 text-center">Connexion</h1>

      <div v-if="serverError" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        {{ serverError }}
      </div>

      <div class="mb-5">
        <label for="email" class="block text-green-800 font-medium mb-2">Adresse mail</label>
        <input
          id="email"
          type="email"
          v-model="form.email"
          placeholder="Adresse mail"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
      </div>

      <div class="mb-5">
        <div class="mb-5 relative">
          <label for="password" class="block text-green-800 font-medium mb-2">Mot de passe</label>
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            placeholder="Min 12c: A1b@caractères"
            class="w-full px-4 py-3 pr-12 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute top-11 right-3 text-green-600 hover:text-green-800 text-xl select-none transition"
          >
            {{ showPassword ? '👁️' : '🙈' }}
          </button>
        </div>
      </div>

      <button
        type="submit"
        :disabled="!isValid"
        class="w-full py-4 rounded-xl text-white text-lg font-semibold bg-green-600 hover:bg-green-700 disabled:bg-green-300 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg"
        @click.prevent="submit"
      >
        Se connecter
      </button>
    </form>
  </div>
</template>

<style scoped></style>
