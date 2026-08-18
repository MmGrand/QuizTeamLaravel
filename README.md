# Quiz Team

Личный кабинет квиз-команды: учёт сыгранных игр с разбивкой по раундам и категориям,
посещаемость участников, статистика и графики прогресса, фотогалерея по играм,
рейтинги и ачивменты.

## Стек

| Слой | Технология |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Auth | Laravel Fortify (вход, регистрация, 2FA, passkeys, verify email) |
| Frontend | Inertia 3 + Vue 3 (Composition API) + TypeScript |
| Стили | Tailwind CSS 4, reka-ui (shadcn-vue), Lucide |
| Роуты в TS | Laravel Wayfinder |
| Тесты | Pest 5 (backend), Vitest (frontend) |
| Качество | Pint, Larastan, ESLint, Prettier |
| Инфраструктура | MySQL 8, Redis 7, Docker Compose |

## Требования

- PHP 8.4 с расширениями `pdo_sqlite`, `pdo_mysql`, `mbstring`, `zip`, `gd`
- Composer 2
- Node.js 24
- Docker (опционально — для запуска с MySQL и Redis)

## Запуск

### Локально (SQLite, без Docker)

```bash
composer setup      # install, .env, key:generate, migrate, npm install, build
composer run dev    # serve + queue:listen + vite
```

Приложение — http://localhost:8000.

### Через Docker (MySQL + Redis)

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

Compose переопределяет `DB_*`, `CACHE_STORE`, `QUEUE_CONNECTION` и `REDIS_HOST`
из `.env` на сервисы `mysql` и `redis` — сам `.env` править не нужно.

Сервис `queue` поднимает `queue:work`. Он обязателен: тяжёлые задачи (конверсии
изображений, генерация итогов сезона) выполняются в очереди, а не синхронно.

## Проверки

```bash
composer test         # config:clear + pint --test + phpstan + artisan test
composer ci:check     # то же самое + eslint + prettier + vue-tsc + vitest
```

По отдельности:

```bash
php artisan test        # Pest
npm run test            # Vitest
npm run test:watch      # Vitest в watch-режиме
composer lint           # Pint с автофиксом
npm run lint            # ESLint с автофиксом
npm run format          # Prettier
```
