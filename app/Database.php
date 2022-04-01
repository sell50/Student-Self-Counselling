<?php

final class Database
{
    public string $erMessage = '';
    private ?PDO $pdo = null;
    private ?PDOStatement $stmt = null;

    // Establish connection to Database
    function __construct()
    {
        try {
            // Using PDO over Mysqli since PDO works with more than just mysql databases
            // These same aspects can we rearranged for mysqli instances
            $this->pdo = new PDO(
                'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Error mode to use
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Use associative array instead of object
                ]
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    // Close connection with database
    function __destruct()
    {
        //Remove existing statements
        if ($this->stmt !== null) {
            $this->stmt = null;
        }
        //Remove pdo instance (close db connection)
        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }

    function select($sql, $cond = null): bool|array
    {
        try {
            $this->stmt = $this->pdo->prepare($sql); // Prepare sql statemnt
            $this->stmt->execute($cond); // Execute sql statement with interpolated data
            return $this->stmt->fetch(); //Get selected items
        } catch (Exception $ex) {
            $this->erMessage = $ex->getMessage();
            return false;
        }
    }

    function selectAll($sql, $cond = null): bool|array
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($cond);
            return $this->stmt->fetchAll();
        } catch (Exception $ex) {
            $this->erMessage = $ex->getMessage();
            return false;
        }
    }

    function execute($sql, $cond = null): bool
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
            return $this->stmt->execute($cond);
        } catch (Exception $ex) {
            $this->erMessage = $ex->getMessage();
            return false;
        }
    }
}
