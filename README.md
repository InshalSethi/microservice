# Laravel + Vue.js Microservices Project

This project is a microservices application with APIs built in Laravel and a frontend implemented in Vue.js.

## Prerequisites

- PHP 7.4 or higher
- Composer
- Node.js and npm
- MySQL or another compatible database

## Installation

Follow these steps to set up and run the project:

1. Clone the repository:
   ```
   git clone [your-repository-url]
   cd [your-project-name]
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```
   If you encounter any issues, try:
   ```
   npm install --force
   ```

4. Copy the `.env.example` file to `.env` and configure your database settings:
   ```
   cp .env.example .env
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Run database migrations:
   ```
   php artisan migrate
   ```

7. Seed the database with initial roles:
   ```
   php artisan db:seed --class=RoleSeeder
   ```

8. Generate JWT secret:
   ```
   php artisan jwt:secret
   ```

   Alternatively, you can manually add the following line to your `.env` file:
   ```
   JWT_SECRET="Some Key"
   ```

## Running the Application

1. Start the Laravel development server:
   ```
   php artisan serve
   ```

2. In a separate terminal, compile and hot-reload the Vue.js frontend:
   ```
   npm run dev
   ```

The application should now be running. Access the Laravel API at `http://localhost:8000` and the Vue.js frontend at `http://localhost:3000` (or whichever port is specified by your Vue.js setup).

## Unit Testing

To set up and run unit tests, follow these steps:

1. Create a `.env.testing` file with the database name set to "testing". For example:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=testing  # Ensure the database name matches the one you are using
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Run the migrations for the testing environment:
   ```
   php artisan migrate --env=testing
   ```

3. To run the tests, use the following command:
   ```
   php artisan test
   ```

## API Documentation (L5 Swagger)

To set up and view the API documentation using L5 Swagger, follow these steps:

1. Install Swagger by running the following command (if not already present in composer.json):
   ```
   composer require darkaonline/l5-swagger
   ```

2. Publish the Swagger configuration file:
   ```
   php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
   ```

3. Generate the Swagger documentation:
   ```
   php artisan l5-swagger:generate
   ```

4. You can now view the API documentation by visiting:
   ```
   http://your-domain-name/api/documentation
   ```
   For example: `http://127.0.0.1:8000/api/documentation`

**Important Notice:** We are making some improvements to the APIs, such as changing POST methods to GET for APIs that only fetch records. These updates will be implemented soon.

## Additional Information

- The Laravel API is located in the `api` directory (adjust if different in your project structure).
- The Vue.js frontend is located in the `frontend` directory (adjust if different in your project structure).
- Make sure to configure CORS in your Laravel application if the frontend and backend are served from different domains.

## Troubleshooting

If you encounter any issues during installation or running the application, please check the following:

- Ensure all prerequisites are installed and up to date.
- Verify that your `.env` file is correctly configured.
- Check the Laravel and Vue.js log files for any error messages.

For more detailed information, refer to the [Laravel documentation](https://laravel.com/docs) and [Vue.js documentation](https://vuejs.org/guide/introduction.html).
