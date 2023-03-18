<!--suppress ALL -->
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Padlet23

A simple padlet that allows users to create collections.

Backend: https://padlet23.s2010456022.student.kwmhgb.at
Backend: http://padlet23.s2010456022.student.kwmhgb.at
API: http://padlet23.s2010456022.student.kwmhgb.at/api

Open ssh connection to Hetzner:

```bash
ssh sstud112@dedi50.your-server.de -p222
```

```bash
cd public_html/padlet/padletServer
```

## Installation

Clone the repository

```bash
git clone
```

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Laravel Commands
```bash
php ../composer.phar dump-autoload

php artisan make:controller BookController --api
php artisan make:migration create_books_table --table=books
php artisan make:factory BookFactory
php artisan make:seeder BooksTableSeeder
php artisan make:model Book
php artisan make:resource BookResource
php artisan make:middleware Admin

php artisan migrate:refresh --seed
php artisan migrate:rollback
php artisan db:seed --class=BooksTableSeeder
php artisan tinker
php artisan serve
php artisan route:list
php artisan cache:clear

```


```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret
```
