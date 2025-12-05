import { storeAuthentification } from '@/assets/store.ts'
import { userData } from '@/assets/userData.ts'

export async function isLoggedIn() {
  if (userData.email === null || userData.password === null) {
    return false;
  }
  const result = await storeAuthentification.login(userData.email, userData.password);
  return result.success;
}
