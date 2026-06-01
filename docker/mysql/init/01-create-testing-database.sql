CREATE DATABASE IF NOT EXISTS app_celrem_testing
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON app_celrem_testing.* TO 'app_celrem'@'%';
