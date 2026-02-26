# Budget Management API

A Symfony 7.4 based REST API for managing budgets and expense requests.
Built with [API Platform](https://api-platform.com/) and Doctrine ORM.
JWT authentication is provided by LexikJWTAuthenticationBundle.

## Features

- Budget creation/update with department association and currency support
- Employee expense submission, cancellation and soft-delete
- Manager approval/rejection flow with comments
- Reporting endpoint with role-based filtering and status filters
- Automatic remaining balance calculation upon expense approval
- Pagination on budget and expense listings
- Asynchronous logging of approval decisions via Messenger & Monolog
- Data fixtures for budgets/expenses/users/departments/roles

## Requirements

- PHP 8.2+
- Composer
- MySQL (or compatible database)
- Node.js (optional, for frontend or documentation)

## Setup

1. **Clone repository**
   ```bash
   git clone `https://github.com/anishbudhathoki21/budget-mgmt-api`  budget-mgmt-api
   cd budget-mgmt-api
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment variables**
   Copy `.env.example` to `.env` and adjust values
   ```bash
   cp .env.example .env
   ```

4. **Generate application keys** (if using encryption)
   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

5. **Create database**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Load fixtures (optional)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

7. **Run development server**
   ```bash
   symfony server:start
   # or
   php -S localhost:8000 -t public
   ```

8. **Access API documentation**
   Visit `http://localhost:8000/api/docs` for Swagger/Hydra docs.

## Authentication

- Obtain a JWT token via `/api/login` (configured through lexik bundle). Defaults to existing fixtures user credentials: `manager@example.com / password123`, `employee@example.com / password123`.
- Attach `Authorization: Bearer <token>` header to requests.


## Design Decisions

- **Custom Providers & Processors**: Used to encapsulate business rules (role filtering, soft delete, status checks) outside of controllers.
- **DTOs** ensure clean input/output separation; prevents domain leaking and simplifies validation.
- **Voters** enforce authorization (`EXPENSE_VIEW`, `EXPENSE_CANCEL`, `EXPENSE_APPROVE`).
- **Soft-delete**: `deletedAt` timestamp on Expense; excluded in queries.
- **Automatic remaining balance**: computed property based on approved expenses. No denormalized column to avoid inconsistency.
- **Pagination**: manually implemented in providers due to custom queries. API Platform global defaults also configured.
- **Messenger logging**: Expense approval decisions dispatched to decouple from HTTP request.
- **Monolog bundle**: for structured logging to `var/log/dev.log`.
- **Data fixtures** provide realistic test data, making manual testing easier.

## .env.example

Environment configuration sample: see `.env.example`.

## Development Tips

- Run `php bin/console lint:twig` to check templates (if added)
- Use `php bin/console debug:router` and `debug:container` for troubleshooting
- Clear cache after configuration changes: `php bin/console cache:clear`

## Testing

Currently there are no automated tests included; api testing can be done from `http://127.0.0.1:8000/api/docs`.

---

