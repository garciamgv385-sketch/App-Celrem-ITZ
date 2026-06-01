# Sistema de control Celrem

Aplicación Laravel para administrar operaciones del taller Celrem: autenticación, clientes y módulos operativos en crecimiento.

## Requisitos

- PHP compatible con el proyecto.
- Composer.
- Node.js y npm.
- Docker Desktop.

## Instalación

```bash
composer install
npm.cmd install
```

Configura el archivo `.env` a partir de `.env.example` si todavía no existe:

```bash
copy .env.example .env
php artisan key:generate
```

## Base de datos con Docker

El proyecto usa un contenedor MySQL 8.4 definido en `docker-compose.yml`.

```bash
docker compose up -d mysql
```

La conexión local de Laravel queda configurada con estos valores:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=app_celrem
DB_USERNAME=app_celrem
DB_PASSWORD=app_celrem_secret
```

Ejecuta las migraciones:

```bash
php artisan migrate
```

## Levantar el proyecto

En una terminal:

```bash
php artisan serve
```

En otra terminal:

```bash
npm.cmd run dev
```

La aplicación quedará disponible en:

```text
http://127.0.0.1:8000
```

## Pruebas

Los tests están configurados para usar MySQL en la base `app_celrem_testing`.

```bash
php artisan test
```

## Esquema SQL

El esquema SQL actual se guarda en:

```text
database/schema/mysql-schema.sql
```

Cuando agregues nuevas migraciones o tablas, ejecuta las migraciones y regenera el SQL con:

```powershell
php artisan migrate
docker exec app_celrem_mysql mysqldump -uapp_celrem -papp_celrem_secret --host=localhost --no-data --routines --triggers --no-tablespaces --skip-comments --skip-add-locks --skip-set-charset --databases app_celrem | Out-File -FilePath database\schema\mysql-schema.sql -Encoding utf8
```

Para importar ese esquema en una base nueva desde PowerShell:

```powershell
Get-Content database\schema\mysql-schema.sql | docker exec -i app_celrem_mysql mysql -uapp_celrem -papp_celrem_secret
```
