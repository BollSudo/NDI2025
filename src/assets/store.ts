import { reactive } from 'vue'
import axios from 'axios'
import { userData } from '@/assets/userData.ts'


export const storeAuthentification = reactive({
  utilisateurConnecte: null,
  login(email: string, password: string): Promise<{ success: boolean, error?: string }> {
    // 1. Payload en 2ème paramètre, pas 1er
    return fetch(`${import.meta.env.VITE_BACKEND_URL}/api/auth`, {
      method: 'POST',  // 2. Ajouter method: 'POST'
      body: JSON.stringify({  // 3. body au lieu de email/password directement
        email: email,
        password: password
      }),
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',  // 4. 'include' au lieu de true
    })
      .then(reponsehttp => {
        if (!reponsehttp.ok) {
          return reponsehttp.json()
            .then(reponseJSON => {
              return { success: false, error: reponseJSON.message };
            })
            .catch(() => ({ success: false, error: 'Erreur serveur' }));  // 5. Gestion erreur JSON
        } else {
          return reponsehttp.json()
            .then(reponseJSON => {
              this.utilisateurConnecte = reponseJSON;
              return { success: true };
            });
        }
      })
      .catch(error => ({  // 6. Gestion erreur réseau
        success: false,
        error: error.message
      }));
  },
});

export async function logout() {
  await axios.post(
    `${import.meta.env.VITE_BACKEND_URL}/api/token/invalidate`,
    {
      refresh_token: localStorage.getItem('refresh_token'),
    },
    {
      headers: { 'Content-Type': 'application/json' },
      withCredentials: true,
    }
  )
  userData.email = null;
  userData.firstName = null;
  userData.token_exp = null;
}

export async function refresh() {
  await axios.post(
    `${import.meta.env.VITE_BACKEND_URL}/api/token/refresh`,
    {
      refresh_token: localStorage.getItem('refresh_token'),
    },
    {
      headers: { 'Content-Type': 'application/json' },
      withCredentials: true,
    }
  )
}
