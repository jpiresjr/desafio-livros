CREATE DATABASE IF NOT EXISTS livros_test
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON livros_test.*
    TO 'livros_user'@'%';
