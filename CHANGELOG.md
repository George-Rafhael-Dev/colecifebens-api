# Changelog

## [Unreleased]

### Added
- REST API with full CRUD for Users, Categories, Products, Orders and Reviews
- Eloquent Models with relationships for all entities
- Database migrations with foreign keys, constraints and indexes
- Request validation on all endpoints
- Business rules on Orders: status flow, payment validation, stock control, users cannot purchase their own products
- Business rules on Reviews: only allowed after delivered and paid order, no duplicates
- `ForceJsonResponse` middleware
- Docker setup with MySQL and automatic migrations on container start
- Users: CPF and phone format validation
- Seeders for each entities

### Changed
- Replaced JSON file persistence with MySQL via Eloquent
- Orders: only PATCH allowed for status updates
- Reviews: no update allowed

### Fixed
- Password no longer exposed in API responses
- Duplicate email and CPF blocked on user creation and update
- Duplicate review blocked at database level

### Removed
- JSON persistence files (`storage/app/data/`)