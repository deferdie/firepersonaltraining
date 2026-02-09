# Personal Training Fitness App

A comprehensive personal training platform built with Laravel, Inertia.js, and Vue 3.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Web Server**: Nginx
- **Frontend**: Vue 3 (Composition API) + Inertia.js
- **Database**: MySQL 8.0
- **Cache/Sessions**: Redis
- **Styling**: Tailwind CSS v4
- **Build Tool**: Vite

## Quick Start

### Using Docker (Recommended)

```bash
# Start all services
docker-compose up -d

# Install PHP dependencies
docker-compose exec app composer install

# Install JavaScript dependencies
docker-compose exec app npm install
# or
docker-compose exec app pnpm install

# Copy environment file
docker-compose exec app cp .env.example .env

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Seed database (optional)
docker-compose exec app php artisan db:seed

# Start Vite dev server
docker-compose exec app npm run dev

# Access at http://localhost:8000
```

### Local Setup

See [CONTRIBUTING.md](CONTRIBUTING.md) for local setup instructions.

## Project Structure

```
resources/js/
  ├── Pages/              # Vue page components
  ├── Components/         # Reusable components (Atomic Design)
  │   ├── atoms/         # Basic building blocks
  │   ├── molecules/     # Simple combinations
  │   ├── organisms/     # Complex components
  │   └── templates/     # Page layouts
  ├── Layouts/           # App layouts
  └── Composables/       # Vue composables
```

## Documentation

- [Architecture.md](Architecture.md) - System architecture and patterns
- [CONTRIBUTING.md](CONTRIBUTING.md) - Development guidelines
- [PROJECT_PLAN.md](PROJECT_PLAN.md) - Feature roadmap
- [PAGES.md](PAGES.md) - Page specifications

## Development

```bash
# Run tests
docker-compose exec app php artisan test

# Access Tinker
docker-compose exec app php artisan tinker

# View logs
docker-compose logs -f nginx
docker-compose logs -f app
```

## License

MIT
