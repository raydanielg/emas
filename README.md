# eMAS — Examination Marking and Results System

eMAS is a focused system for supervising and semi-automating examination marking, and simplifying results compilation and publication. It aims to make the workflow fast, transparent, and maintainable.

- Built with Laravel and modern, minimal UI (Tailwind CSS).
- Development environment is pre-configured for local use.
- Clear structure for extending modules (marking, moderation, reports, exports).

> Note: This repository is prepared for development use. Production hardening (SSO, rate limiting, audit logs, etc.) is intentionally out of scope for now.

## Login credential
 > enteres of data logins:
    username: demo@test.com
    password: Password123!
> Headmaster logins 
   username: headmaster1
   password: Pass@123

## Getting Started (Development)

1. Requirements: PHP 8.2+, Composer, SQLite/MySQL (optional), Git.
2. Install dependencies:
   - `composer install`
3. Environment:
   - Copy `.env.example` to `.env` and adjust `APP_URL`, database creds if needed.
   - Generate key: `php artisan key:generate`.
4. Run locally:
   - `php artisan serve` (or) `php -S 0.0.0.0:8000 -t public/`.
5. Open http://127.0.0.1:8000/login

## Current Scope

- UI: Custom sign-in and forgot-password pages (Tailwind).
- Auth (UI only for now): username + password forms; backend wiring pending.
- Branding: eMAS logo + SVG favicon.

## Roadmap (High-level)

- Authentication backed by database (username login).
- Roles/permissions for markers, reviewers, and admins.
- Marking workflows and moderation.
- Results verification, exports, and dashboards.

## Collaboration

Created by: Ezra Daniel

- Collaboration welcome. Fork the repo and open a Pull Request.
- For coordination, reach out on WhatsApp: +255613976254
- Issues and feature requests are also welcome via GitHub Issues.

## License

This project includes the Laravel framework, which is MIT licensed.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
