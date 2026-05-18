# Third-Party Sources & Attributions - Laravel API

### Libraries & Frameworks
- **Laravel 12:** The PHP Framework for Web Artisans. [Documentation](https://laravel.com/docs/12.x)
- **Laravel Sanctum:** API token authentication. [Documentation](https://laravel.com/docs/12.x/sanctum)
- **Spatie Laravel-Permission:** Role and permission management for teachers/students. [GitHub](https://github.com/spatie/laravel-permission)
- **Filament v3:** Admin panel for managing users and system resources. [Documentation](https://filamentphp.com/)
- **Scramble:** Automatic OpenAPI documentation for Laravel (Zero-annotation). [GitHub](https://github.com/dedoc/scramble)
- **Laravel Pint:** Opinionated PHP code style fixer (Linting). [Documentation](https://laravel.com/docs/12.x/pint)
- **LibreTranslate:** Self-hosted machine translation engine (Auxiliary Service). [GitHub](https://github.com/LibreTranslate/LibreTranslate)

### Infrastructure & Tools
- **Nginx Proxy Manager:** Reverse proxy and SSL management. [Website](https://nginxproxymanager.com/)
- **Docker & Docker Compose:** Containerization and service orchestration. [Documentation](https://docs.docker.com/)
- **MySQL 8.0:** Relational database management system. [Documentation](https://dev.mysql.com/doc/)

### Code Snippets & Logic
- **Internal Translation Bridge:** Logic for interacting with the LibreTranslate REST API was adapted from the [LibreTranslate API Docs](https://libretranslate.com/docs/).
- **Deployment & Restart Policies:** Docker `unless-stopped` implementation based on the [Docker Compose Specification](https://docs.docker.com/engine/containers/start-containers-automatically/).
