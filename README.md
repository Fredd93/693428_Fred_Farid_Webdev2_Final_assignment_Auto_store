# Grand Transmission Auto

Web Development 2 final assignment project by Fred and Farid.

This is a dockerized full-stack car dealership application with:
- a Vue 3 frontend
- a PHP backend API
- a MariaDB database
- nginx as the web server

## Submission Checklist

This project zip contains:
- frontend source code in `frontend/`
- backend source code in `app/`
- SQL database scripts in `grand_transmission_auto.sql` and `grand_transmission_auto_v2.sql`
- this `README.md` with setup steps and login credentials
- Docker configuration in `docker-compose.yml`, `PHP.Dockerfile`, and `nginx.conf`

## Tech Stack

- Frontend: Vue 3, Vite, Pinia, Vue Router
- Backend: PHP 8, JWT auth, PDO
- Database: MariaDB / MySQL
- Web Server: nginx
- Tooling: Docker Compose, MailHog, phpMyAdmin, Swagger UI

## Requirements

- Docker Desktop
- Optional: add this hosts entry if you want to use the named local route:

```text
127.0.0.1 grandtransmissionauto.local
```

## How To Run

1. Open the project folder in a terminal.
2. Start the containers:

```bash
docker compose up --build -d
```

3. Import the database script:

Windows PowerShell:

```powershell
Get-Content .\grand_transmission_auto_v2.sql | docker compose exec -T mysql mysql -udeveloper -psecret123 grand_transmission_auto
```

4. Open the application:

- Main app: `http://localhost/`
- Preferred route: `http://grandtransmissionauto.local/home`

## Available URLs

- Main application: `http://localhost/`
- Named route: `http://grandtransmissionauto.local/home`
- MailHog: `http://localhost:8025/`
- phpMyAdmin: `http://localhost:8080/`
- Swagger UI: `http://localhost:8090/`

## Default Login Accounts

All seeded accounts use the same password:

```text
password
```

Accounts:

- Admin: `admin@gta.com`
- Employee: `employee@gta.com`
- Client: `client@gta.com`

## Database Notes

- Database name: `grand_transmission_auto`
- Database user: `developer`
- Database password: `secret123`
- The main submission SQL file is `grand_transmission_auto_v2.sql`
- `grand_transmission_auto.sql` is the earlier/base schema kept in the repo as reference

## Project Structure

```text
app/                          PHP backend
app/docs/openapi.yaml         API documentation
frontend/                     Vue frontend
docker-compose.yml            Docker services
PHP.Dockerfile                PHP container build
nginx.conf                    nginx config
grand_transmission_auto.sql   original SQL script
grand_transmission_auto_v2.sql final SQL script
README.md                     setup and login instructions
```

## Notes For Assessment

- The application is dockerized as required by the assignment.
- The frontend is served through nginx after the Vue build completes.
- The PHP backend exposes the API used by the frontend.
- MailHog is included for local email testing during development.

## Environment Variables

The repo includes:
- `.env`
- `.env.example`

The mail credentials can be set there if email sending is tested, but the main application can still be reviewed without using real SMTP credentials.
