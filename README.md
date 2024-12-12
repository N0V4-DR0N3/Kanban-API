# Kanban API

A modern Kanban board API built with Laravel 11.

## Requirements

### Local Development
- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL

### Docker Environment
- Docker
- Docker Compose

## Installation

### Local Setup

1. Clone the repository
```bash
git clone https://github.com/N0V4-DR0N3/Kanban-API.git
cd Kanban-API
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Deploy application
```bash
php artisan deploy --passport --no-optimize --no-composer
```

### Docker Setup

1. Start containers
```bash
docker-compose up -d
```

2. Access container shell
```bash
docker exec -it kb-api /bin/sh
```

3. Run deployment command inside container
```bash
php artisan deploy --passport --no-optimize --no-composer
```

## Contributing
Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting pull requests.

## Authors skeleton

- [Vinicius Rodrigues](https://github.com/N0V4-DR0N3)
- [João Victor](https://github.com/HiddenUserHere)
- Valdir de Lima
- Natan

## License
Licensed under the [MIT license](https://opensource.org/licenses/MIT).
