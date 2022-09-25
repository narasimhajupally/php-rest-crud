<?php
class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_DATABASE_NAME . ';charset=utf8mb4';
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);

            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
