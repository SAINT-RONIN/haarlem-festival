<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Provides a shared PDO database connection for the entire application.
 *
 * Centralizes database connection creation so all repositories and services share
 * the same connection. This ensures transactions work correctly across components
 * and avoids creating multiple connections per request.
 */
class Database
{
    /*
     * Purpose: Holds the cached PDO connection instance (singleton pattern).
     */
    private static ?PDO $pdo = null;

    /**
     * Returns a shared PDO connection instance (singleton pattern).
     *
     * Creates and caches a single PDO connection so all database operations share it.
     * This is critical for transactions - if different parts of the code used different
     * connections, transactions wouldn't work properly.
     *
     * @throws RuntimeException When database connection fails or environment variables are missing
     */
    public static function getConnection(): PDO
    {
        // Return existing connection if already created
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // Load database credentials from environment variables (.env file)
        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $charset = 'utf8mb4';

        // Validate required environment variables are set
        if (!$host || !$db || !$user || $pass === false) {
            throw new RuntimeException(
                'Missing database environment variables. Ensure DB_HOST, DB_NAME, DB_USER, and DB_PASS are set in .env'
            );
        }

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                // Throw exceptions on errors instead of silent failures
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Return associative arrays by default
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Use native prepared statements
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return self::$pdo;
        } catch (PDOException $e) {
            // Wrap in RuntimeException so callers get context without exposing credentials
            throw new RuntimeException('Database connection failed', 0, $e);
        }
    }
}