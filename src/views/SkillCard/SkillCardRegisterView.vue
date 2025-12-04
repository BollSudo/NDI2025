
<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import SkillCardNav from '@/components/SkillCardNav.vue'
import axios from 'axios'
import router from '@/router'
const submitting = ref(false)
const success = ref(false)
const serverError = ref(null)

interface FormData {
  name: string
  firstName: string
  email: string
  indicatif: string
  phoneNumber: string
  birthdate: string
  password: string
  confirmpassword: string
}

const form = reactive<FormData>({
  name: '',
  firstName: '',
  email: '',
  indicatif: '+33',
  phoneNumber: '',
  birthdate: '',
  password: '',
  confirmpassword: ''
})

const errors = reactive<Record<string, string>>({})
const showPassword = ref(false)

const isValid = computed(() => {
  return !Object.values(errors).some(error => error) &&
    form.name && form.firstName && form.email && form.password && form.confirmpassword
})

const validatename = () => {
  const regex = /^[a-zA-ZàâäéèêëïîôöùûüÿçñÆŒæœ'-]+$/
  if (!form.name) {
    errors.name = 'Le name est obligatoire'
  } else if (!regex.test(form.name)) {
    errors.name = 'Seuls les lettres et apostrophes sont autorisés'
  } else {
    delete errors.name
  }
}

const validatefirstName = () => {
  const regex = /^[a-zA-ZàâäéèêëïîôöùûüÿçñÆŒæœ'-]+$/
  if (!form.firstName) {
    errors.firstName = 'Le prénom est obligatoire'
  } else if (!regex.test(form.firstName)) {
    errors.firstName = 'Seuls les lettres et apostrophes sont autorisés'
  } else {
    delete errors.firstName
  }
}

const validateEmail = () => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.email) {
    errors.email = "L'adresse mail est obligatoire"
  } else if (!regex.test(form.email)) {
    errors.email = 'Format email invalide'
  } else {
    delete errors.email
  }
}

const validatephoneNumber = () => {
  if (!form.phoneNumber) {
    delete errors.phoneNumber
    return
  }

  const regexFR = /^(0|\+33\s?)[1-9](?:\s?\d{2}){4}$/

  const cleanNumber = form.phoneNumber.replace(/[\s.\-_]/g, '')

  if (!regexFR.test(form.phoneNumber) || cleanNumber.length !== 10) {
    errors.phoneNumber = 'Format invalide (ex: 06 12 34 56 78)'
  } else {
    delete errors.phoneNumber
  }
}


const validatepassword = () => {
  if (!form.password) {
    errors.password = 'Le mot de passe est obligatoire'
  } else {
    const issues: string[] = []

    if (form.password.length < 12) {
      issues.push('Minimum 12 caractères requis')
    }
    if (form.password.length > 255) {
      issues.push('Maximum 255 caractères')
    }

    if (!/[A-Z]/.test(form.password)) {
      issues.push('1 majuscule requise (A-Z)')
    }

    if (!/[a-z]/.test(form.password)) {
      issues.push('1 minuscule requise (a-z)')
    }

    if (!/[0-9]/.test(form.password)) {
      issues.push('1 chiffre requis (0-9)')
    }

    if (!/[^A-Za-z0-9]/.test(form.password)) {
      issues.push('1 caractère spécial requis (!@#$%^&*)')
    }

    if (issues.length > 0) {
      errors.password = issues.join(', ')
    } else {
      delete errors.password
    }
  }

  // Re-valider la confirmation si elle existe déjà
  if (form.confirmpassword) {
    validateConfirmpassword()
  }
}
const validateConfirmpassword = () => {
  if (!form.confirmpassword) {
    errors.confirmpassword = 'La confirmation est obligatoire'
  } else if (form.confirmpassword !== form.password) {
    errors.confirmpassword = 'Les mots de passe ne correspondent pas'
  } else {
    delete errors.confirmpassword
  }
}

const handleSubmit = () => {
  if (isValid.value) {
    console.log('Formulaire valide:', form)
    alert('Compte créé avec succès !')
  }
}

async function submit() {
  serverError.value = null
  submitting.value = true
  try {
    const payload = {
      email: form.email,
      password: form.password,
      name: form.name,
      firstName: form.firstName,
      phoneNumber: form.phoneNumber || null,  // null si vide
      birthdate: form.birthdate || null,      // null si vide
    }

    await axios.post(`${import.meta.env.VITE_BACKEND_URL}/user`, payload, {
      headers: { 'Content-Type': 'application/json' }
    })

    success.value = true
    setTimeout(() => router.push('/skill_card/login'), 1500)
  } catch (err: any) {
    console.log('Erreur complète:', err.response?.data) // Debug

    // ✅ MESSAGE GÉNÉRAL (ex: "Cet email est déjà utilisé.")
    if (err.response?.data?.message) {
      serverError.value = err.response.data.message
    }
    // ✅ VIOLATIONS VALIDATION (ex: email, password...)
    else if (err.response?.data?.violations) {
      const violations = err.response.data.violations
      serverError.value = violations.map((v: any) =>
        `${v.propertyPath}: ${v.message}`
      ).join('; ')
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
  <SkillCardNav />
  <div class="flex items-center justify-center w-full">
    <form @submit.prevent="handleSubmit" class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-10">
      <h1 class="text-3xl font-semibold text-green-700 mb-10 text-center">
        Formulaire de création de compte
      </h1>

      <div v-if="success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        Compte créé avec succès !
      </div>

      <div v-if="serverError" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        {{ serverError }}
      </div>

      <div class="mb-5">
        <label for="name" class="block text-green-800 font-medium mb-2">Nom</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          placeholder="Nom"
          @input="validatename"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
        <p v-if="errors.name" class="mt-2 text-red-600 text-sm">{{ errors.name }}</p>
      </div>

      <div class="mb-5">
        <label for="firstName" class="block text-green-800 font-medium mb-2">Prénom</label>
        <input
          id="firstName"
          v-model="form.firstName"
          type="text"
          placeholder="Prénom"
          @input="validatefirstName"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
        <p v-if="errors.firstName" class="mt-2 text-red-600 text-sm">{{ errors.firstName }}</p>
      </div>

      <div class="mb-5">
        <label for="email" class="block text-green-800 font-medium mb-2">Adresse mail</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          placeholder="exemple@mail.com"
          @input="validateEmail"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
        <p v-if="errors.email" class="mt-2 text-red-600 text-sm">{{ errors.email }}</p>
      </div>

      <div class="mb-5">
        <label for="phoneNumber" class="block text-green-800 font-medium mb-2">
          Numéro de téléphone <span class="text-gray-500 text-sm font-normal">(optionnel)</span>
        </label>
        <div class="flex gap-2">
          <select
            v-model="form.indicatif"
            class="w-28 px-2 py-3 rounded-lg border-2 border-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent bg-white transition"
          >
            <option value="+33">+33</option>
            <option value="+1">+1</option>
            <option value="+41">+41</option>
            <option value="+49">+49</option>
          </select>
          <input
            id="phoneNumber"
            v-model="form.phoneNumber"
            type="tel"
            placeholder="6 12 34 56 78"
            @input="validatephoneNumber"
            class="flex-1 min-w-0 px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
          />
        </div>
        <p v-if="errors.phoneNumber" class="mt-2 text-red-600 text-sm">{{ errors.phoneNumber }}</p>
      </div>

      <div class="mb-5">
        <label for="birthdate" class="block text-green-800 font-medium mb-2">
          Date de naissance <span class="text-gray-500 text-sm font-normal">(optionnel)</span>
        </label>
        <input
          id="birthdate"
          v-model="form.birthdate"
          type="date"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
      </div>

      <div class="mb-5 relative">
        <label for="password" class="block text-green-800 font-medium mb-2">Mot de passe</label>
        <input
          id="password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          placeholder="Min 12c: A1b@caractères"
          @input="validatepassword"
          class="w-full px-4 py-3 pr-12 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
        <button
          type="button"
          @click="showPassword = !showPassword"
          class="absolute top-11 right-3 text-green-600 hover:text-green-800 text-xl select-none transition"
        >
          {{ showPassword ? '👁️' : '🙈' }}
        </button>
        <p v-if="errors.password" class="mt-2 text-red-600 text-sm">{{ errors.password }}</p>
      </div>

      <div class="mb-10">
        <label for="confirmpassword" class="block text-green-800 font-medium mb-2">Confirmer mot de passe</label>
        <input
          id="confirmpassword"
          v-model="form.confirmpassword"
          :type="showPassword ? 'text' : 'password'"
          placeholder="Répétez le mot de passe"
          @input="validateConfirmpassword"
          class="w-full px-4 py-3 border-2 border-green-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
        />
        <p v-if="errors.confirmpassword" class="mt-2 text-red-600 text-sm">{{ errors.confirmpassword }}</p>
      </div>

      <button
        type="submit"
        :disabled="!isValid"
        class="w-full py-4 rounded-xl text-white text-lg font-semibold bg-green-600 hover:bg-green-700 disabled:bg-green-300 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg"
        @click.prevent="submit"
      >
        {{ isValid ? 'Enregistrer' : 'Complétez le formulaire' }}
      </button>
    </form>
  </div>
</template>
