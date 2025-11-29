# Users Directory

A Laravel + Inertia.js + Vue.js application for managing users with advanced features:

- User CRUD (Create, Read, Update, Delete)
- Filtering and search (by user fields and address)
- Pagination
- Caching for user details
- Queue jobs for cache management
- Animated, responsive dashboard UI
- Batch seeding for large datasets

## Features

- **User CRUD:** Create, view, update, and delete users with address info
- **Filtering:** Search by first name, email, country, city, etc.
- **Pagination:** Navigation through large user lists
- **Caching:** Cache  for user details and paginated results
- **Jobs:** Laravel queue jobs for cache population and invalidation
- **Animations:** Smooth UI transitions and modal dialogs
- **Seeder:** Batch seeding for millions of users

## Tech Stack

- Laravel 12.x (PHP 8.4+)
- Inertia.js
- Vue.js 3
- Tailwind CSS
- Redis (Optional)

## Getting Started

### Prerequisites
- PHP >= 8.4
- Composer
- Node >= 22
- Redis server (Optional, but if set you need to update .env)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/molero3111/users_directory
   cd users-directory
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

4. **Copy .env and configure:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations and seeders:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

7. **On separate terminal, start the queue worker (for jobs):**
   ```bash
   php artisan queue:work
   ```

8. **Build frontend assets:**
   ```bash
   npm run dev
   ```

## Usage

- Access the dashboard at [http://localhost:8000](http://localhost:8000)
- Use the search bar to filter users
- Click user cards to view, edit, or delete
- Create new users via the modal
- Pagination and filters are cached for fast access

## Running Unit Tests

After setting up and running the app, it is recommended to run unit tests to verify core functionality:

```bash
php artisan test
```

This will execute all unit and feature tests, including tests for user CRUD.

## Project Structure

- `app/Http/Controllers/UserController.php` — User CRUD, filtering, caching
- `app/Jobs/CacheUsersJob.php` — Caches users in Redis
- `app/Jobs/RemoveUserCacheJob.php` — Removes user from cache
- `resources/js/pages/Dashboard.vue` — Main dashboard UI
- `resources/js/components/UserModal.vue` — User details modal
- `resources/js/components/SearchBar.vue` — Search/filter UI
- `database/seeders/DatabaseSeeder.php` — Batch seeding logic
- `config/seeder.php` — Seeder configuration