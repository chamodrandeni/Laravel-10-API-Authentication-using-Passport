# Laravel 10 API Authentication using Passport (Dockerized)

This repository demonstrates a modern approach to building secure API authentication in Laravel 10 using Passport, fully containerized with Docker for easy local development and deployment.

## Project Overview

This project provides a ready-to-use Laravel 10 application with API authentication powered by Laravel Passport (OAuth2). The application is pre-configured to run in Docker containers, making it easy to set up, develop, and test in any environment.

**Features:**
- Laravel 10 API with Passport OAuth2 authentication
- User registration, login, email verification, profile, and logout endpoints
- PostgreSQL database support (can be adapted for MySQL)
- Docker Compose setup for PHP-FPM, Nginx, and database services
- Example Makefile for common Docker tasks

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)
- (Optional) [Git](https://git-scm.com/)

### Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/<your-github-username>/Laravel-10-API-Authentication-using-Passport.git
    cd Laravel-10-API-Authentication-using-Passport/docker-for-Laravel
    ```

2. **Copy and configure environment files:**
    - Edit the `.env` and files in `envs/` as needed for your database and mail settings.

3. **Build and start the containers:**
    ```bash
    make up
    ```

4. **Install dependencies and set up Laravel:**
    ```bash
    make app
    make key
    ```

5. **Run migrations:**
    ```bash
    docker compose -f docker-compose-dev.yml run app php artisan migrate
    ```

6. **Install Passport:**
    ```bash
    docker compose -f docker-compose-dev.yml run app php artisan passport:install
    ```

7. **Access the application:**
    - The API will be available at [http://localhost:8000](http://localhost:8000).

## API Endpoints

- `POST /api/v1/register` — Register a new user
- `POST /api/v1/login` — Login and receive access/refresh tokens
- `GET /api/v1/profile` — Get authenticated user profile (requires token)
- `GET /api/v1/logout` — Logout and revoke tokens
- `POST /api/v1/account/verify` — Verify user email

## Configuration Notes

- The project uses PostgreSQL by default, but you can adapt it for MySQL by editing the environment files and Docker Compose.
- Passport client credentials are set in the `.env` file after running `php artisan passport:install`.
- Nginx and PHP-FPM are configured for production-like local development.

## Security Notice

**Do not commit real secrets or credentials to public repositories.**
The provided environment files use placeholders. Set your own secure values before deploying or running in production.

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## License

This project is open-sourced under the MIT license.
