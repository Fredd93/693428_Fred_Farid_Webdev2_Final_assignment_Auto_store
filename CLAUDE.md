# Grand Transmission Auto — CLAUDE.md

## Additional Documentation

Design docs, architecture notes, and specs live in `docs/`. Add a new `@` import below whenever you drop a new file there.

@docs/architecture.md
@docs/rolls-royce.design.md

---

## Project Overview

Full-stack car dealership web app built as a Web Dev 2 final assignment. The project has two distinct frontends served by the same Nginx container:

- **PHP/Blade-style frontend** (`app/public/`) — server-rendered pages with vanilla JS for interactivity
- **Vue 3 SPA** (`frontend/src/`) — built with Vite, served from `/` by Nginx after `npm run build`

The PHP layer doubles as a REST API consumed by both frontends.

---

## Stack

| Layer | Technology |
|---|---|
| Web server | Nginx |
| Backend | PHP (custom router via `steampixel/simplePHPRouter`) |
| Database | MariaDB (MySQL-compatible) |
| ORM/DB | PDO — raw SQL, no ORM |
| Auth | JWT (`firebase/php-jwt`) + PHP sessions for the server-rendered pages |
| Email | PHPMailer → SMTP (MailHog in dev) |
| Frontend SPA | Vue 3 + Vite + Pinia |
| API docs | Swagger UI (`app/docs/openapi.yaml`) |

---

## Running the Project

```bash
# Start all services
docker compose up --build

# Rebuild the Vue SPA only
docker compose up --build vue_build
```

| URL | Service |
|---|---|
| `http://localhost` | Main app (Nginx → PHP + Vue dist) |
| `http://localhost:8080` | phpMyAdmin |
| `http://localhost:8025` | MailHog (caught emails) |
| `http://localhost:8090` | Swagger UI |
| `http://localhost:3306` | MariaDB (direct) |

Email credentials (`MAIL_USER`, `MAIL_PASS`) must be set in a `.env` file or as shell env vars before running `docker compose up`.

---

## Directory Structure

```
.
├── app/
│   ├── public/             # PHP application root (Nginx points here)
│   │   ├── index.php       # Entry point — bootstraps routes
│   │   ├── api/            # API controllers and standalone endpoints
│   │   │   ├── CarApiController.php
│   │   │   ├── OrderApiController.php
│   │   │   ├── get_cars.php        # Paginated car listing endpoint
│   │   │   ├── car_filter.php      # Filter options endpoint
│   │   │   └── utils/              # Auth, mail, response helpers
│   │   ├── controllers/    # Page controllers (render views)
│   │   ├── models/         # PDO models (BaseModel → CarModel, OrderModel, UserModel)
│   │   ├── dto/            # Data Transfer Objects (CarDTO, OrderDTO, UserDTO)
│   │   ├── views/
│   │   │   ├── pages/      # Full page views (cars.php, carDetail.php, …)
│   │   │   └── partials/   # Reusable partials (navbar, footer, modals)
│   │   ├── routes/
│   │   │   ├── index.php   # Page routes (GET /, /cars, /car/:id, …)
│   │   │   └── api.php     # API routes (/api/cars, /api/orders, …)
│   │   ├── lib/
│   │   │   ├── Route.php   # simplePHPRouter wrapper
│   │   │   └── env.php     # Hard-coded env defaults (overridden by Docker env)
│   │   └── assets/
│   │       ├── css/
│   │       ├── js/         # Vanilla JS (carFilter.js, carDetails.js, …)
│   │       └── images/     # Uploaded car images (served statically)
│   ├── docs/
│   │   └── openapi.yaml    # OpenAPI spec (served by Swagger container)
│   └── vendor/             # Composer dependencies (do not edit)
├── frontend/               # Vue 3 SPA
│   └── src/
│       ├── views/          # Page-level Vue components
│       ├── components/     # Shared UI components
│       ├── stores/         # Pinia stores (auth, cars)
│       ├── router/         # Vue Router config
│       └── api/            # Axios/fetch client wrappers
├── docs/                   # Project-level documentation for Claude and contributors
├── docker-compose.yml
├── PHP.Dockerfile
└── nginx.conf
```

---

## Database

**DB name:** `grand_transmission_auto`  
**Default credentials:** `developer` / `secret123` (set in `docker-compose.yml` and `app/public/lib/env.php`)

### Key Tables

| Table | Purpose |
|---|---|
| `cars` | Car inventory — brand, model, year, price, on_sale, discount, image_path, transmission, engine_spec, color, condition, description, lease_available, lease_terms, status |
| `users` | Customers and employees — role field distinguishes them |
| `orders` | Purchase/lease orders linked to a car and user |

Images are stored as filenames in `cars.image_path` and served from `app/public/assets/images/`.

---

## API Reference

Full spec at `app/docs/openapi.yaml`. Key endpoints:

| Method | Path | Description |
|---|---|---|
| GET | `/api/get_cars` | Paginated + filtered car list. Params: `brand`, `year`, `transmission`, `on_sale`, `price_min`, `price_max`, `page`, `limit` (default 12) |
| GET | `/api/car_filter` | Returns filter dropdown options (brands, years, transmissions, price bounds) |
| GET | `/api/cars` | All cars (no pagination) |
| GET | `/api/cars/:id` | Single car by ID |
| POST | `/api/cars` | Insert new car (auth required) |
| GET | `/api/cars/edit` | Update car details |
| GET | `/api/car/delete/:id` | Delete car |
| POST | `/api/login` | Returns JWT; stored client-side for SPA auth |
| GET | `/api/orders` | All orders (employee only) |
| POST | `/api/orders/create` | Create order |
| PUT | `/api/orders/status` | Update order status |

---

## Auth Model

Two auth mechanisms co-exist:

1. **JWT** — issued by `/api/login`, used by the Vue SPA via `Authorization: Bearer <token>` header. Validated in `app/public/api/utils/AuthApiController.php`.
2. **PHP sessions** — used by the server-rendered pages. The `/order` route checks `$_SESSION['role'] === 'employee'`.

Secret key is `APP_SECRET` env var (default: `gta_jwt_secret_change_in_prod` — **change in production**).

---

## Routing

Routing is handled by a thin wrapper around [steampixel/simplePHPRouter](https://github.com/steampixel/simplePHPRouter):

- `app/public/lib/Route.php` — the router class
- `app/public/routes/index.php` — page routes
- `app/public/routes/api.php` — API routes
- `app/public/index.php` — entry point that loads routes and calls `Route::run('/')`

---

## Frontend (Vanilla JS) Notes

- `assets/js/carFilter.js` — populates filter dropdowns from `/api/car_filter.php`, then calls `/api/get_cars` with pagination (Prev/Next). `LIMIT = 12` is hardcoded.
- `assets/js/carDetails.js` — loads single car detail via `/api/cars/:id`
- `assets/js/Orders.js` — employee order management
- `assets/js/addCar.js` / `assets/js/car.js` — admin car CRUD

---

## Common Gotchas

- `app/public/lib/env.php` sets fallback env vars — these are overridden by Docker env at runtime. Don't rely on the hardcoded values outside local dev.
- Car images are uploaded to `app/public/assets/images/` with a generated filename prefix. The `image_path` column stores only the filename, not a full path.
- The Vue SPA and the PHP pages are **separate** — the SPA talks to `/api/*` via fetch/axios, the PHP pages include assets directly.
- `MAIL_USER` and `MAIL_PASS` are **not** in `docker-compose.yml` — they must come from a `.env` file or the host environment.
