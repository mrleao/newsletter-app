# 📰 NewsApp — Laravel + Vue (Inertia) + Docker

> **O que tem de mais??** Docker configurado com **SSL**, **Cloudflare** e **R2 Bucket ou S3** para armazenar imagens.

---

## 🚀 Stack

* **Laravel** (PHP 8.4-FPM)
* **Vue 3 + Vite + Inertia**
* **Nginx** (proxy)
* **MySQL** (ou MariaDB)
* **Node + npm** dentro do container PHP (para `npm run dev`)

> Container PHP (nome: `newsapp`) roda Composer, Artisan e Vite.

---

## ✅ Pré-requisitos

* Docker & Docker Compose (v2+)
* Porta **80** livre (Nginx) e **5173** livre (Vite)
* Conta no cloudeflare para configurar as keys e conseguir salvar/atualizar imagens

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

# 9) Subir o Vite (dev)
npm run dev


