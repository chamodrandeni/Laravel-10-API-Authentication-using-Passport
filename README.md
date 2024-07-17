Laravel 10 API Authentication using Passport

This repository demonstrates how to set up API authentication using Laravel Passport in a Laravel 10 application. Passport provides a full OAuth2 server implementation for your Laravel application in a matter of minutes, making it simple to implement and use API authentication.

Table of Contents
Installation
Configuration
Usage
Endpoints

Installation
Follow these steps to set up the project:

 Clone the repository:
  "git clone https://github.com/chamodrandeni/Laravel-10-API-Authentication-using-Passport.git"
  "cd Laravel-10-API-Authentication-using-Passport"
  
 Install dependencies:
  "composer install"

 Generate the application key:
  "php artisan key:generate"
  
 Set up your database and update the .env file with your database credentials.

 Run migrations:
  "php artisan migrate"
  
 Install Passport:
  "php artisan passport:install"
  
Configuration
 Add the Laravel\Passport\HasApiTokens trait to your App\Models\User model. This trait provides helper methods to your model to inspect the authenticated user’s token and scopes.

  use Laravel\Passport\HasApiTokens;
  
  class User extends Authenticatable
  {
      use HasApiTokens, Notifiable;
  }
  
 In the config/auth.php configuration file, you should set the api driver to passport. This will instruct your application to use Passport’s TokenGuard when authenticating incoming API requests:

  'guards' => [
      'web' => [
          'driver' => 'session',
          'provider' => 'users',
      ],
  
      'api' => [
          'driver' => 'passport',
          'provider' => 'users',
      ],
  ],
  
Usage
Register a new user:

POST /api/register
Log in to obtain an access token:

POST /api/login
Use the access token to authenticate requests to your API:

GET /api/user
Include the access token in the Authorization header as a Bearer token.

Endpoints

POST /api/register - Register a new user
POST /api/login - Log in and obtain an access token
POST /api/logout - Log out the authenticated user
GET /api/user - Retrieve the authenticated user’s information
