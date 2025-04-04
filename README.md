# Проєкт: Поштові Індекси (Slim + Vue 3 + Swagger)

Цей проєкт реалізує API для роботи з поштовими індексами України (Ukrposhta), з фронтендом на Vue 3 і документацією через Swagger.

---

## Встановлення та запуск

### 1. Інсталювати Composer

Переконайтесь, що Composer встановлений:

```bash
composer --version
```

> Якщо не встановлено — завантажити: https://getcomposer.org/download/

---

### 2. Запустити Docker (MySQL)

```bash
docker compose up -d
```

> У проєкті має бути `docker-compose.yml`, який піднімає MySQL

---

### 3. Встановити PHP-залежності

```bash
composer install
```

---

### Імпорт поштових індексів з архіву:

### Створення таблиці:

```bash
php migrations/migrate.php
```

```bash
php import/import_zip.php
```

---

### 4. Запустити Slim-сервер

```bash
php -S localhost:8000 public/index.php
```

Slim-додаток буде доступний за адресою:

📍 http://localhost:8000

---

### 5. Перейти в папку з фронтендом

```bash
cd frontend
```

---

### 6. Інсталювати залежності Node.js

```bash
npm install
```

---

### 7. Запустити фронтенд

```bash
npm run dev
```

Vue-додаток буде доступний за адресою:

🌐 http://localhost:5173

> ⚠️ Фронтенд використовує проксі для API: `/api/*` → `http://localhost:8000`

---

## 📘 Swagger документація

1. Відкрити у браузері: [http://localhost:8000/docs](http://localhost:8000/docs)
2. Побачиш Swagger UI з методами API:
    - `GET /api/postcodes`
    - `POST /api/postcodes`
    - `DELETE /api/postcodes/{code}`
3. Якщо бачиш `Fetch error /openapi.yaml`, перевір:
    - що існує `docs/openapi.yaml`
    - маршрут `/openapi.yaml` зареєстрований у `src/routes/swagger.php`

---

