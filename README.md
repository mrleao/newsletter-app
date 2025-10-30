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
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=newsapp
DB_USERNAME=root
DB_PASSWORD=root

# Arquivos e sessão
FILESYSTEM_DISK=public
SESSION_DRIVER=file

# Vite / HMR
VITE_PORT=5173
VITE_APP_URL=http://localhost
```

> Se for Postgres, troque `DB_CONNECTION`, porta e credenciais.

---

## 🧩 Vite (rodando no container)

Para HMR funcionar via Docker, deixe o Vite acessível na rede:

```js
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  server: {
    host: true, // 0.0.0.0
    port: parseInt(process.env.VITE_PORT) || 5173,
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

---

## 📁 Estrutura útil

```
public/               # assets públicos
public/storage -> storage/app/public (symlink)
resources/            # Vue/Blade/Styles
storage/app/public    # uploads (não versionar)
storage/app/private   # arquivos privados (não web)
```

> **Não** versione o conteúdo de `storage/`. Use `.env.example` e **`php artisan storage:link`** para servir uploads.

---

## 🔧 Comandos úteis

```bash
# Shell no container PHP
docker exec -it newsapp bash

# Logs
docker compose logs -f nginx

# Reset do banco (cuidado!)
php artisan migrate:fresh --seed

# Cache/config
php artisan config:clear && php artisan route:clear && php artisan view:clear

# Build de produção
docker exec -it newsapp bash -lc "npm ci && npm run build"
```

---

## 🧪 Pacotes do Front mais comuns

```bash
npm i vue-select @vueup/vue-quill quill
```

Uso:

```js
// Exemplo Quill (VueUp)
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

// Exemplo vue-select
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
```

---

## 🛡️ Permissões

```bash
# No host ou dentro do container
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
```

---

## 🧰 Troubleshooting (erros comuns)

### 1) `could not find driver` (PDO)

* Garanta que a imagem PHP instalou `pdo_mysql`.
* Confirme: `php -m | grep pdo_mysql` dentro do container.
* Confira `DB_HOST` (nome do serviço no docker‑compose), usuário e senha.
* Espere o banco subir: `docker compose logs -f mysql`.

### 2) `413 Request Entity Too Large` (uploads no Nginx)

No vhost Nginx, aumente o limite:

```nginx
client_max_body_size 20m;
client_body_timeout 60s;
```

Recarregue o Nginx e tente de novo.

### 3) Vite não atualiza / HMR não conecta

* Libere a porta `5173` no `docker-compose.yml` (ex: `5173:5173`).
* Use `server.host = true` e `hmr.host = 'localhost'` no `vite.config.js`.
* Verifique se `APP_URL`/`VITE_APP_URL` apontam para `http://localhost`.

### 4) `@vueup/vue-quill` / `vue-select` não encontrados

* Rode `npm install` (ou `npm i @vueup/vue-quill quill vue-select`).
* Reinicie o Vite (`Ctrl+C` e `npm run dev`).

### 5) Storage sem servir imagens

* Rode `php artisan storage:link`.
* Use `Storage::url('path/no_image.png')` (para `storage/app/public`).
* Para fallback estático, use `asset('assets/image/no_image.png')` (arquivo em `public/assets/...`).

### 6) Node/NPM no build do container (exit 100)

* No Dockerfile, rode o script da NodeSource e **instale no mesmo RUN**:

```Dockerfile
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm i -g npm@latest
```

* Evite limpar o cache **antes** da instalação.

---

## 🧯 Dicas de Git (storage)

`.gitignore` recomendado:

```gitignore
/storage/*
!/storage/app/.gitignore
!/storage/framework/.gitignore
!/storage/logs/.gitignore
/public/storage
```

---

## 🏁 Fluxo diário de dev (resumo)

1. `docker compose up -d`
2. `docker exec -it newsapp bash`
3. `npm run dev` (deixe rodando)
4. Codar feliz ⚡

---

## 🌐 URLs

* App: **[http://localhost](http://localhost)**
* Vite (dev assets/HMR): **[http://localhost:5173](http://localhost:5173)**

---

## 📜 Licença

Este projeto segue a licença definida no repositório
