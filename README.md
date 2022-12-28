## Includes:
- Passport set up and configured (https://laravel.com/docs/9.x/passport)
- Permissions and roles set up and configured (https://spatie.be/docs/laravel-permission/v3/basic-usage/basic-usage)
    - Database seeding to create 4 users associated to 4 roles, basic permissions and assignment of users to these roles
- Positive/Negative feature tests for users (login, updating, creation, deletion, roles)
- Soft-deletes with soft-cascading on deletes
- API response messages fetched from locales
- API documentation at https://laravel-boilerplate.readme.io/reference
- Packages to:
    - Create seeds from existing database (https://github.com/orangehill/iseed)
    - Create models from existing database tables (https://github.com/krlove/eloquent-model-generator)
    - 
## Requires:
- PHP 8.*
- MySQL
- 
## Installation:
```0: Create database if it doesn't exist already or ensure that it's empty if it does exist (drop tables, leave database intact)
   1: composer install
   2: composer dumpautoload
   3: php artisan cache:forget spatie.permission.cache
   4: php artisan optimize:clear
   5: php artisan migrate:fresh --seed
   6: php artisan passport:install
   ```
