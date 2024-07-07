# MoviesApp

# PREREQUEST
Make sure you have the following installed on your local machine:

PHP (>= 7.3)
Composer
PGSQL (or any other preferred database system)

# INSTALLATION

## Clone the repository
git clone <repository-url>

## Navigate into the project directory
cd MoviesApp

## Install PHP dependencies
composer install

## Copy .env.example to .env and generate application key
cp .env.example .env
php artisan key:generate

## Configure your database in the .env file

## Run database migrations and seed (if applicable)
php artisan migrate --seed

# CONFIGURATION FOR DATABASE

## Open .env file and set the database connection details:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

## Run database migrations:
php artisan migrate

## Start the development server:
php artisan serve

Application should now be running at http://localhost:8000.

RUNNING TESTS

To run the automated tests for this system, use:
php artisan test

