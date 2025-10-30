# 📰 NewsApp — Laravel + Vue (Inertia) + Docker

Um guia **boladão** pra subir o projeto do zero, desenvolver com hot‑reload e evitar as tretas mais comuns.

---

## 🚀 Stack

* **Laravel** (PHP 8.4‑FPM)
* **Vue 3 + Vite + Inertia**
* **Nginx** (proxy)
* **MySQL** (ou MariaDB)
* **Node + npm** dentro do container PHP (para `npm run dev`)

> Container PHP (nome: `newsapp`) roda Composer, Artisan e Vite.

---

## ✅ Pré‑requisitos

* Docker & Docker Compose (v2+)
* Porta **80** livre (Nginx) e **5173** livre (Vite)

---

## 🧱 Subindo o ambiente

```bash
# 1) Build (limpo)
docker compose build --no-cache

# 2) Subir containers
docker compose up -d

# 3) Entrar no container PHP (newsapp)
docker exec -it newsapp bash   # (ou sh)

# 4) Dependências do backend
composer install

# 5) Dependências do front
npm install

# 6) Copiar o .env
cp .env.example .env

# 7) Gerar APP_KEY
php artisan key:generate

# 8) Rodar migrações (ajuste DB no .env antes)
php artisan migrate

# 9) Linkar storage público (uploads)
php artisan storage:link

# 10) Subir o Vite (dev)
npm run dev
```

Acesse: **[http://localhost](http://localhost)**

> Dica: mantenha um terminal no `npm run dev` e outro no `docker compose logs -f` para acompanhar os logs.

---

## ⚙️ Configuração do `.env`

```dotenv
APP_NAME="NewsApp"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Banco (ajuste conforme seu docker-compose)
DB_CONNECTION=mysql
DB_HOST=mysql         #container
DB_PORT=3306
DB_DATABASE=newsapp
DB_USERNAME=newsapp
DB_PASSWORD=newsapp

# Arquivos e sessão
FILESYSTEM_DISK=public
SESSION_DRIVER=file

```

> Se for Postgres, troque `DB_CONNECTION`, porta e credenciais.

---

## 🧩 Vite (rodando no container)

Para HMR funcionar via Docker, deixe o Vite acessível na rede:

```js
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    host: true,
    hmr: { host: 'localhost' },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
})
```

