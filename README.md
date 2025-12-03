# Nuit de l'info - Édition 2025

Nom de l'équipe: **Bureau des savoirs magiques**

Lien vers la page: **https://ndi2025.bollmaster.com/**

Fallback: **...**

Dates: 04/12 16h34 - 05/12 8h04

## Membres de l'équipe
- Alexandre DESCHANEL *as [...]()*
- Nathan LOPEZ *as [...]()*
- Julien RENAUD *as [BollSudo](https://github.com/BollSudo)* (capitaine)
- Héloïse RIGAUX *as [...]()*
- Badis SAAD *as [...]()*
- Kilyan SOMBE *as [...]()*

---

## Sommaire
>1. [Défis](#défis)
>2. [Développement](#développement)
>3. [Tech Stack](#tech-stack)

## Défis
- [Défi de la nuit 2025](...)
- [Défi 2](...)

## Développement

### Prérequis
- **Docker** ou **node (v.24) + npm** 

Installer *Docker* : https://docs.docker.com/engine/install/   
Installer *nodejs* : https://nodejs.org/en/download

---

### Lancement

Pour lancer le serveur de développement avec *Docker*, à la racine du projet : 

```bash
docker compose -f docker-compose-dev.yml up
```

*Par défaut, le serveur web est accessible depuis l'hôte sur : http://localhost:5173 (configuration dans [`docker-compose-dev.yml`](./docker-compose-dev.yml))*

Pour lancer le serveur de développement avec *npm*, à la racine du projet : 

```bash
npm install && npm run dev
```

## Tech Stack

- **Framework**: [Vue.js](https://vuejs.org/)
- **CSS**: [tailwindcss](https://tailwindcss.com/)
- **Build**: [npm](https://www.npmjs.com/) + [Vite](https://vitejs.fr/)
- **Conteneurisation**: [Docker](https://www.docker.com/)
- **CI/CD**: [GitHub Actions](https://docs.github.com/en/actions)
