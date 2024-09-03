


## Setup

Same way you would install a typical laravel application.

    composer install

    npm install

    npm run dev

    php artisan serve

The UI is displayed on the root page

## Testing

### Vue

- Run Vue tests using `npm run test` 
- Run Laravel tests using `php artisan test`

## Extra Notes

A command exists to send batched orders at the end of each month. A schedule has been created in `console.php` to run on the first day of every month at 1am

You can test the command by running this command in the project console: `php artisan app:batch-orders`
