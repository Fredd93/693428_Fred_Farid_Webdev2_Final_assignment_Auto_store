# Grand Transmission Auto — Web Development 2

> **693428 · Fred & Farid · Inholland University · Web Development 2**

A full-stack vehicle dealership platform built for the Web Development 2 assignment.
Clients can browse, purchase, or lease vehicles. Employees manage inventory and orders.
Administrators manage users and roles.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Vue 3, Vite, Pinia, Vue Router 4, Tailwind CSS |
| Backend | PHP 8, PSR-4 namespaces, firebase/php-jwt |
| Database | MariaDB / MySQL |
| Web Server | nginx |
| Dev Tools | Docker Compose, phpMyAdmin, MailHog, Swagger UI |

---

## Getting Started

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Run the project

```bash
git clone https://github.com/Fredd93/693428_Fred_Farid_Webdev2_Final_assignment_Auto_store.git
cd 693428_Fred_Farid_Webdev2_Final_assignment_Auto_store
docker-compose up --build
```

Import the database:
```bash
docker exec -i $(docker-compose ps -q mysql) \
  mysql -udeveloper -psecret123 grand_transmission_auto \
  < grand_transmission_auto_v2.sql
```

### URLs

| Service | URL |
|---|---|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8080/api |
| MailHog (email testing) | http://localhost:8025 |
| Swagger API Docs | http://localhost:8090 |

---

## Default Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@gta.com | password |
| Employee | employee@gta.com | password |
| Client | client@gta.com | password |

> Passwords are stored as bcrypt hashes in the database — the plaintext passwords above are only provided here for assessment purposes.

---

## Project Structure

```
├── app/                    PHP backend (PSR-4, GTA\ namespace)
│   ├── public/index.php    Single entry point
│   ├── src/
│   │   ├── Controllers/    CarController, OrderController, UserController, AuthController
│   │   ├── Models/         CarModel, OrderModel, UserModel, BaseModel
│   │   ├── Middleware/     AuthMiddleware (JWT validation + role checks)
│   │   └── Routes/         api.php, auth.php
│   └── docs/openapi.yaml   OpenAPI 3.0 spec
├── frontend/               Vue 3 SPA (Vite + Pinia + Vue Router + Tailwind)
│   └── src/
│       ├── views/          HomeView, CarsView, CarDetailView, LoginView, RegisterView,
│       │                   DashboardView, AdminCarsView, AdminOrdersView, AdminUsersView
│       ├── components/     Navbar, Footer, CarCard, Pagination, StatusBadge
│       ├── stores/         auth.js (JWT + user), cars.js
│       ├── router/         Vue Router with auth + role guards
│       └── api/client.js   Axios with Bearer token interceptor
├── docker-compose.yml
├── nginx.conf
└── grand_transmission_auto_v2.sql
```

---

## User Roles

| Role | Permissions |
|---|---|
| **Client** | Browse cars, place purchase/lease orders, view own orders, manage own profile |
| **Employee** | All client permissions + add/edit cars, process all orders |
| **Admin** | All employee permissions + delete cars, manage users, assign roles |

---

## API Reference

Full interactive documentation available at **http://localhost:8090** (Swagger UI).

All protected endpoints require:
```
Authorization: Bearer <token>
```

Token is obtained from `POST /api/auth/login`.

---

## Dependencies

All dependencies are managed automatically — no manual installs needed:

- **PHP packages** — via Composer (`composer.json` / `composer.lock`)
- **Frontend packages** — via npm (`package.json` / `package-lock.json`)

Docker installs everything on first run. The only requirement on the host machine is **Docker Desktop**.

---

## Environment Variables

Set in `docker-compose.yml` under the `php` service:

| Variable | Description |
|---|---|
| `DB_HOST` | MySQL hostname (default: `mysql`) |
| `DB_NAME` | Database name |
| `DB_USER` | Database user |
| `DB_PASSWORD` | Database password |
| `APP_SECRET` | JWT signing secret — **change in production** |

---

## AI Disclosure Statement

AI tools (Claude) were used during the development of this project to assist with:
- Structuring the PHP PSR-4 namespace architecture
- Writing boilerplate for JWT middleware and Axios interceptors
- Generating the OpenAPI specification

All generated code was reviewed, understood, and adapted by the student. The student is able to explain and demonstrate understanding of all code in this project. AI was not used to replace understanding — it was used as a development accelerator.

---

## References

- [Vue.js Official Guide](https://vuejs.org/guide)
- [RESTful API Best Practices](https://restfulapi.net)
- [firebase/php-jwt](https://github.com/firebase/php-jwt)
- [Pinia Documentation](https://pinia.vuejs.org)
