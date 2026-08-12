<?php

/*
	DESC : 2025-03-03  It is suggest to migrate to use PDO to avoid SQL injection and better compatiblility for cross Database project
	
*/

class DB {
    public static $instance = null;
    public $pdo = null;
    public $host;
    public $db;
    public $user;
    public $pass;

    public function __construct($host = null, $db = null, $user = null, $pass = null) {
        $this->host = $host ?? O_HOST;
        $this->db = $db ?? O_DB;
        $this->user = $user ?? O_DBUSER;
        $this->pass = $pass ?? O_DBPWD;
    }
    
    public function beginTransaction() {
        $this->initConnection();
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function initConnection() {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8";
            $this->pdo = new PDO($dsn, $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    private function getFullSql($sql, $data) {
        foreach ($data as $key => $value) {
            // Escape the value for safe output (especially for strings)
            $escapedValue = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $sql = str_replace(":$key", $escapedValue, $sql);
        }
        return $sql;
    }

    public static function getInstance($host, $db, $user, $pass) {
        if (self::$instance === null) {
            self::$instance = new Database($host, $db, $user, $pass);
        }
        return self::$instance;
    }

    public function createTable($tableName, $columns) {
        $this->initConnection();
        //$sql = "CREATE TABLE IF NOT EXISTS $tableName ($columns)";
        //$this->pdo->exec($sql);
    }

    public function insert_db($sql, $params = []) : int{
        $this->pdo_query($sql, $params);
        return (int)$this->pdo->lastInsertId();
    }

    // Method to execute complex SQL with parameter binding
    public function pdo_query($sql,$params = []){
        $this->initConnection();
        $stmt = $this->pdo->prepare($sql);

        // Bind parameters if any
        foreach ($params as $key => &$value) {
            if (is_null($value)) {
                $stmt->bindValue("$key", null);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        // Before executing, show the final query
        $finalQuery = $sql;
        foreach ($params as $key => &$value) {
            $finalQuery = preg_replace('/\s*' . preg_quote($key) . '\b/', $this->pdo->quote($value), $finalQuery);
        }

        if(isset($_SERVER['HTTP_QUERY']) && $_SERVER['HTTP_QUERY'] =='Y'){
            echo "Final Query: " . $finalQuery . "\n"; // Display the final query
            // die();
        }
        

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function insert_db($tableName, $data) {
    //     $this->initConnection();
        
    //     try {
    //         $columns = implode(", ", array_keys($data));
    //         $placeholders = ":" . implode(", :", array_keys($data));
    //         $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
            
    //         $stmt = $this->pdo->prepare($sql);
    //         foreach ($data as $key => &$value) {
    //             if (is_null($value)) {
    //                 $stmt->bindValue(":$key", null, PDO::PARAM_NULL);
    //             } else {
    //                 $stmt->bindValue(":$key", $value);
    //             }
    //         }
    //         $stmt->execute();

    //         // Return the last inserted ID
    //         return $this->pdo->lastInsertId();
    //     } catch (PDOException $e) {
    //         // Log the error message (optional)
    //         error_log($e->getMessage());

    //         // Throw a 500 Internal Server Error
    //         http_response_code(500);
    //         die(json_encode([
    //             'error' => 'Internal Server Error',
    //             'sql' => $this->getFullSql($sql, $data),  // Include the SQL query in the error response
    //             'message' => $e->getMessage() // Include the exception message
    //         ]));
    //     }
    // }

    public function delete($tableName, $condition) {
        $this->initConnection();
        $sql = "DELETE FROM $tableName WHERE $condition";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    public function fetchAll($tableName) {
        $this->initConnection();
        $stmt = $this->pdo->query("SELECT * FROM $tableName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to fetch records with parameter binding
    public function fetch($tableName, $fields = '*', $where = '', $params = []) {
        $this->initConnection();
        $sql = "SELECT $fields FROM $tableName";
        
        if ($where) {
            $sql .= " WHERE $where";
        }

        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters if any
        foreach ($params as $key => &$value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to execute complex SQL with parameter binding
    public function get_Sql($sql, $params = [], $debug=null) {
        return $this->pdo_query($sql, $params);
    }

    // Method to execute complex SQL with parameter binding
    public function get_One($sql, $params = []) {
        $this->initConnection();
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters if any
        foreach ($params as $key => &$value) {
            $stmt->bindValue(":$key", $value);
        }

        // Before executing, show the final query
        $finalQuery = $sql;
        foreach ($params as $key => &$value) {
            $finalQuery = preg_replace('/:\s*' . preg_quote($key) . '\b/', $this->pdo->quote($value), $finalQuery);
        }
        //echo "Final Query: " . $finalQuery . "\n"; // Display the final query

        $stmt->execute();
        $one = $stmt->fetchColumn();
        return $one;
    }

    // Method to execute complex SQL with parameter binding
    public function select($sql, $params = []) {
        $this->initConnection();
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters if any
        foreach ($params as $key => &$value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $one = $stmt->fetchColumn();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to execute complex SQL with parameter binding
    public function update($sql, $params = [] , $debug=null) {
        $this->initConnection();
        $stmt = $this->pdo->prepare($sql);


        // Bind parameters if any
        foreach ($params as $key => &$value) {
            if (is_null($value)) {
                $stmt->bindValue(":$key", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        if($debug){
            $finalQuery = $sql;
            foreach ($params as $key => &$value) {
                // Check for null values
                if (is_null($value)) {
                    // Replace the placeholder with NULL (without quotes)
                    $finalQuery = preg_replace('/:\s*' . preg_quote($key) . '\b/', 'NULL', $finalQuery);
                } else {
                    // Replace the placeholder with the quoted value
                    $finalQuery = preg_replace('/:\s*' . preg_quote($key) . '\b/', $this->pdo->quote($value), $finalQuery);
                }
            }
            echo "Final Query: " . $finalQuery . "\n"; // Display the final query            
        }
        

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}