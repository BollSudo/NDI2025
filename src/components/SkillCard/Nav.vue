<script setup lang="ts">
import router from '@/router'
import { userData } from '@/assets/userData.ts'
import { logout } from '@/assets/store.ts'
import { computed } from 'vue'

const isLoggedIn = computed(() => userData.email !== null);

async function deconnect() {
  await logout();
  setTimeout(() => router.push('/skill_card'), 1500)
}

</script>

<template>
  <nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="mx-auto px-6 py-4 flex justify-evenly space-x-8">
      <a href="/public" class="text-green-700 font-semibold hover:text-green-900 transition">
        Home
      </a>
      <a
        v-if="!isLoggedIn"
        href="/skill_card/register"
        class="text-green-700 font-semibold hover:text-green-900 transition"
      >
        Créer un compte
      </a>
      <a
        v-if="!isLoggedIn"
        href="/skill_card/login"
        class="text-green-700 font-semibold hover:text-green-900 transition"
      >
        Connexion
      </a>
      <a
        v-if="isLoggedIn"
        @click="deconnect"
      > Déconnexion </a>
    </div>
  </nav>
</template>

<style scoped></style>
