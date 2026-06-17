# ColecifeBens API 
REST API for a collectibles marketplace, built with Laravel 10 + MySQL + Docker.
 
## Requirements
- Docker Desktop
- Or: PHP 8.2 + Composer + MySQL (local)
## Running the project
 
### Docker
```bash
docker compose up --build
```
 
### Local
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```
> Set `DB_HOST=localhost` in `.env` and make sure MySQL is running locally.
 
## Documentation
- Swagger UI: `http://localhost:8000/api/documentation`
- Postman: import `colecifebens.postman_collection.json`
## Business Rules
- Stock is decremented automatically when an order is created
- Order status follows the flow: `pendente → enviado → entregue` (or `cancelado`)
- Order cannot be cancelled after delivery or with approved payment
- Payment method can only be changed when status is `pendente` and payment is `aguardando`
- Reviews are only allowed after the order is `entregue` with payment `aprovado`
- One review per user per product
## Tech Stack
- Laravel 10 + PHP 8.2
- MySQL 8
- Docker + Docker Compose
- Swagger (OpenAPI 3.0)