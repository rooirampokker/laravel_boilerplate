[![CircleCI](https://dl.circleci.com/status-badge/img/gh/rooirampokker/laravel_boilerplate/tree/master.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/rooirampokker/laravel_boilerplate/tree/master)
## Includes:
- Passport set up and configured (https://laravel.com/docs/9.x/passport)
- Permissions and roles set up and configured (https://spatie.be/docs/laravel-permission/v5/basic-usage/basic-usage)
  - Users can have multiple roles with different permissions
  - Permissions check passes if any of the assigned roles is authorised to perform the required action
- Positive/Negative feature tests for users and password resets
- Soft-deletes with soft-cascading on deletes
- API response messages fetched from locales
- Provision for localization of emails 
- Implements UUIDs
- API documentation at https://laravel-boilerplate.readme.io/reference
- Packages to:
    - Create seeds from existing database (https://github.com/orangehill/iseed)
    - Create models from existing database tables (https://github.com/krlove/eloquent-model-generator)
## Requires:
- PHP 8.*
- MySQL
## Installation:
```0: Create database if it doesn't exist already or ensure that it's empty if it does exist (drop tables, leave database intact)
   1: composer install
   2: composer dumpautoload
   3: php artisan cache:forget spatie.permission.cache
   4: php artisan optimize:clear
   5: php artisan migrate:fresh --seed
   6: php artisan passport:install
   ```
