<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database singleton class for managing database connections
 */

class Database
{

    private static $instance = null;
    private $connection;
    private $logger;

    /**
     * Private constructor to prevent direct instantiation
     * Establishes database connection using environment variables
     * 
     * @throws \Exception If database connection fails
     */
    private function __construct()
    {
        $this->logger = new Logger();
        try {
            $this->connection = new PDO(
                "pgsql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            $this->logger->info("Database connection established");
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Gets the singleton instance of the Database class
     * 
     * @return Database The single instance of Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Executes a prepared SQL query
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return \PDOStatement The statement after execution
     * @throws \Exception If query execution fails
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $this->logger->debug("Query executed: {$sql}");
            return $stmt;
        } catch (PDOException $e) {
            $this->logger->error("Query failed: {$sql} - Error: " . $e->getMessage());
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetches a single record from the database
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return array|false Single record as associative array or false if not found
     */
    public function find($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetches all matching records from the database
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return array Array of records as associative arrays
     */
    public function findAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }
}
