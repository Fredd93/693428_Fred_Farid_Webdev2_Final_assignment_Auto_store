# Architecture

## System Topology

```
Browser
  │
  ▼
Nginx (port 80)
  ├── /api/*  ──────────────────────► PHP-FPM container
  ├── /assets/* ───────────────────► app/public/assets/ (static files)
  └── /* (non-API, non-asset) ─────► Vue dist (built by vue_build container)
                                         └── falls through to index.html (SPA)
```

The PHP container also serves server-rendered pages for the legacy PHP frontend — Nginx routes those requests when the path resolves to a `.php` file under `app/public/`.

---

## Docker Services

| Service | Image | Role |
|---|---|---|
| `nginx` | `nginx:latest` | Reverse proxy; serves Vue dist and forwards PHP requests |
| `php` | Custom (`PHP.Dockerfile`) | PHP-FPM; handles API and server-rendered pages |
| `vue_build` | Custom (`frontend/Dockerfile`) | Builds the Vue SPA into a shared `vue_dist` volume |
| `mysql` | `mariadb:latest` | Primary database |
| `phpmyadmin` | `phpmyadmin:latest` | Database admin UI (port 8080) |
| `mailhog` | `mailhog/mailhog` | SMTP trap for dev emails (UI port 8025, SMTP port 1025) |
| `swagger` | `swaggerapi/swagger-ui` | Serves OpenAPI docs from `app/docs/openapi.yaml` (port 8090) |

The `vue_dist` Docker volume is the handoff point between `vue_build` and `nginx`. The `mysqldata` volume persists the database across container restarts.

---

## PHP Request Lifecycle

```
app/public/index.php
  │  bootstraps vendor autoload, env, Route class
  │
  ├── app/public/routes/index.php   (page routes)
  └── app/public/routes/api.php     (API routes)
        │
        ▼
      Route::run('/')
        │  matches REQUEST_URI against registered regex patterns
        ▼
      Route callback
        ├── Page routes → require view file (views/pages/*.php)
        └── API routes  → instantiate Controller → call method
                              │
                              ▼
                           Model (PDO)
                              │
                              ▼
                           DTO (returned as array or JSON)
```

### Layer Responsibilities

| Layer | Path | Responsibility |
|---|---|---|
| Entry point | `app/public/index.php` | Load autoloader, env, routes; call `Route::run()` |
| Router | `app/public/lib/Route.php` | Regex-based URL matching (steampixel/simplePHPRouter) |
| Route definitions | `app/public/routes/` | Map URLs to callbacks |
| API Controllers | `app/public/api/` | Parse request, call model, emit JSON |
| Page Controllers | `app/public/controllers/` | Prepare data, include view |
| Models | `app/public/models/` | Raw SQL via PDO; return DTOs |
| DTOs | `app/public/dto/` | Typed value objects passed between model and controller |
| Views (pages) | `app/public/views/pages/` | Full HTML pages (PHP templates) |
| Views (partials) | `app/public/views/partials/` | Reusable fragments (navbar, footer, modals) |

---

## Database Schema

**Database:** `grand_transmission_auto`

### `cars`

| Column | Type | Notes |
|---|---|---|
| `car_id` | INT PK AUTO_INCREMENT | |
| `brand` | VARCHAR | e.g. BMW, Audi |
| `model` | VARCHAR | |
| `year` | INT | |
| `transmission` | VARCHAR | e.g. Automatic, Manual |
| `engine_spec` | VARCHAR | |
| `car_condition` | VARCHAR | new / used |
| `description` | TEXT | |
| `color` | VARCHAR | |
| `price` | DECIMAL | Base price |
| `on_sale` | ENUM('yes','no') | |
| `discount` | DECIMAL | Percentage off when on_sale = yes |
| `lease_available` | ENUM('yes','no') | |
| `lease_terms` | TEXT | |
| `status` | VARCHAR | available / sold / reserved |
| `image_path` | VARCHAR | Filename only; resolved to `assets/images/<image_path>` |
| `created_at` | TIMESTAMP | |

### `users`

| Column | Notes |
|---|---|
| `user_id` | PK |
| `email` | Unique |
| `password` | Bcrypt hash |
| `role` | `customer` or `employee` |

### `orders`

| Column | Notes |
|---|---|
| `order_id` | PK |
| `car_id` | FK → cars |
| `user_id` | FK → users |
| `status` | pending / approved / rejected |
| `created_at` | |

---

## Authentication

Two mechanisms run in parallel:

### JWT (Vue SPA)

```
POST /api/login
  └── AuthApiController validates credentials
        └── firebase/php-jwt signs token with APP_SECRET
              └── Token returned to client
                    └── Stored client-side; sent as Authorization: Bearer <token>
                          └── Protected API endpoints verify token via AuthApiController
```

### PHP Sessions (Server-rendered pages)

- Login sets `$_SESSION['logged_in']` and `$_SESSION['role']`.
- Protected page routes (e.g. `/order`) check the session inline before rendering.

---

## Vue SPA Architecture

```
frontend/src/
├── main.ts           Entry point; mounts App.vue, registers router and Pinia
├── router/index.js   Vue Router — maps paths to views; guards check auth store
├── stores/
│   ├── auth.js       Pinia store — JWT token, user info, login/logout actions
│   └── cars.js       Pinia store — car list, filters, pagination state
├── api/client.js     Axios/fetch wrapper — attaches JWT header automatically
├── views/            Page-level components (one per route)
└── components/       Shared UI (Navbar, Footer, CarCard, Pagination, …)
```

The SPA is built by Vite into `frontend/dist/`, copied into the `vue_dist` Docker volume, and served by Nginx for all non-API, non-asset routes. Vue Router handles client-side navigation.

---

## Pagination Design

`GET /api/get_cars` implements server-side pagination:

- **Query params:** `page` (default 1), `limit` (default 12), plus filter params.
- **Response envelope:**
  ```json
  { "cars": [...], "total": 42, "page": 2, "limit": 12, "totalPages": 4 }
  ```
- **Model methods:** `getFilteredCarsCount($filters)` for the total, `getFilteredCarsPaginated($filters, $page, $limit)` for the page slice.
- **Client:** `assets/js/carFilter.js` renders Prev/Next controls and re-fetches on navigation.

---

## Image Upload Flow

1. Employee submits the add-car form with image files.
2. `app/public/api/add_car.php` (or `CarApiController`) moves uploaded files to `app/public/assets/images/` with a generated filename prefix.
3. Only the filename is stored in `cars.image_path`.
4. Images are served by Nginx directly from the `assets/images/` path.

---

## Email Flow

PHPMailer sends transactional email via SMTP:

- **Dev:** MailHog catches all outgoing mail (no real emails sent). View at `http://localhost:8025`.
- **Prod:** Set `MAIL_USER` and `MAIL_PASS` env vars pointing to the Gmail SMTP account (`grandtransmissionsautos@gmail.com`).
- Service wrapper: `app/public/api/utils/MailService.php`.
