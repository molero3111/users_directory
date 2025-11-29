# Running Users Directory Locally with Docker

This guide explains how to run the Users Directory project using Docker, with steps tailored for local development. It covers repository setup, environment configuration, database and Redis setup, and seeding options.

## 1. Clone the Repository

```bash
git clone https://github.com/molero3111/users_directory.git
cd users_directory
```

## 2. Copy and Configure Environment Variables

Copy the example environment file and update it for Docker usage:

```bash
cp .env.example .env
```

### Update `.env` for Docker Services
Set the following variables for PostgreSQL and Redis:

#### Database (PostgreSQL)
```
DB_CONNECTION=pgsql
DB_HOST=db-users
DB_PORT=5432
DB_DATABASE=users_directory
DB_USERNAME=admin
DB_PASSWORD=admin
```

#### Redis
```
REDIS_CLIENT=phpredis
REDIS_HOST=redis-users
REDIS_PASSWORD=null
REDIS_PORT=6379
BROADCAST_CONNECTION=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 3. Build and Start Docker Containers

```bash
docker compose up --build -d
```

This will start the app, database, and Redis containers.

## 4. Run Migrations and Seeders

After containers are running, run migrations and seeders inside the app container:

```bash
docker compose exec api-users php artisan migrate --seed
```
### Note on seeders
You can control the seeder batch size and total number of users via environment variables:
```
SEEDER_BATCH_SIZE=5000
SEEDER_TOTAL=1000000
```

If you encounter memory issues during seeding, lower the `SEEDER_BATCH_SIZE` value.

## 6. Access the Application

The app will be available at [http://localhost:8000](http://localhost:8000)

---

**Tip:** For more details, see the main `README.md`.
