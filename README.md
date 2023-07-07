[![CircleCI](https://dl.circleci.com/status-badge/img/gh/rooirampokker/laravel_boilerplate/tree/master.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/rooirampokker/laravel_boilerplate/tree/master)
## Includes:
- Passport set up and configured (https://laravel.com/docs/10.x/passport)
- Permissions and roles set up and configured (https://spatie.be/docs/laravel-permission/v5/basic-usage/basic-usage)
  - Users can have multiple roles with different permissions
  - Permissions check passes if any of the assigned roles is authorised to perform the required action
- Positive/Negative feature tests for users and password resets
- Soft-deletes with soft-cascading on deletes (https://github.com/Askedio/laravel-soft-cascade)
- Multi-tenancy (https://spatie.be/docs/laravel-multitenancy/v3/introduction)
- API response messages fetched from locales
- Provision for localization of emails 
- Implements UUIDs
- Code Sniffer (phpcs) and linter (phpcbf) (https://github.com/squizlabs/PHP_CodeSniffer)
```
./vendor/bin/phpcs --standard=PSR12 /var/www/html/eventogy_v2/app/
./vendor/bin/phpcbf --standard=PSR12 /var/www/html/eventogy_v2/app/
```
- Packages to:
    - Create seeds from existing database (https://github.com/orangehill/iseed)
    - Create models from existing database tables (https://github.com/krlove/eloquent-model-generator)
## Requires:
- PHP 8.2
- MySQL
## Installation:
```0: Create database if it doesn't exist already or ensure that it's empty if it does exist (drop tables, leave database intact)
   1: create `.env` based on `.env.example` and update necessary constants 
   2: composer install
   3: composer dumpautoload
   4: php artisan cache:forget spatie.permission.cache
   5: php artisan optimize:clear
   6: php artisan migrate 
   7: php artisan db:seed
   8: php artisan tenants:seed
   9: php artisan passport:keys
   
   ```
