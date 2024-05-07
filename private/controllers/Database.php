<?php

class Database
{
    private PDO $connection;
    private string $host;
    private string $user;
    private string $password;
    private string $database;
    function __construct()
    {
        global $database;
        $this->host = $database['host'];
        $this->user = $database['user'];
        $this->password = $database['password'];
        $this->database = $database['database'];

        self::connect($this->host, $this->user, $this->password, $this->database);
    }

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @return void
     */
    function connect(string $host, string $user, string $password, string $database)
    {
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    }

    /**
     * This function is used to prepare a query
     * 
     * Example:
     * $stmt = Database::prepare("SELECT * FROM users WHERE id = ?");
     * 
     * This will prepare a query to get all users with id 1
     * 
     * @param $sql
     * @return mixed
     */
    function prepare(string $sql)
    {
        return $this->connection->prepare($sql);
    }

    /**
     * This function is used to get the last inserted ID
     * 
     * Example:
     * $lastInsertId = Database::lastInsertId();
     * 
     * This will return the last inserted ID
     * @return mixed
     */
    function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * This function is used to insert data into the database
     * 
     * Example:
     * Database::insert('users', ['name', 'email'], ['John Doe', 'john.doe@gmail.com']);
     * 
     * This will insert a new user into the users table with name John Doe and email
     * 
     * @param string $table
     * @param array $columns
     * @param array $values
     * @return void | int
     * @throws ErrorException
     */
    public static function insert(string $table, array $columns, array $values, $connection = (new Database))
    {// use foreach use ani sql injection use ?
        $sql = "INSERT INTO $table (";
        foreach ($columns as $column) {
            $sql .= "$column, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ") VALUES (";
        foreach ($values as $value) {
            $sql .= "?,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= ")";
        $sql = htmlspecialchars($sql);
        $stmt = $connection->prepare($sql);
        try {
            $stmt->execute($values);
            // Get the last inserted ID
            $lastInsertId = $connection->lastInsertId();
            return $lastInsertId;
        } catch (Exception $e) {
            // Construct error message including SQL query and values
            $errorMessage = "Error executing SQL query: " . $stmt->queryString . ". Values: " . json_encode($values) . ". Exception: " . $e->getMessage();

            throw new ErrorException($errorMessage);
        }
    }

    /**
     * This function is used to get data from the database
     * 
     * Example:
     * Database::get('users', ['name', 'email'], [], ['id' => 1]);
     * This will return the name and email of the user with id 1
     * Database::get('users', ['name', 'email'], [], ['id' => 1], 'name DESC');
     * This will return the name and email of the user with id 1 ordered by name in descending order
     * Databse::get('users', ['name', 'email'], ['user_roles' => 'users.role_id = user_roles.id'], ['users.id' => 1]);
     * This will return the name and email of the user with id 1 with a join on user_roles table
     * 
     * @param string $table
     * @param array $columns
     * @param $join
     * @param array $where
     * @param string $orderBy
     * @return mixed
     */
    public static function getAll(string $table, array $columns = ['*'],$join = [], array $where = [], string $orderBy = '')
    {
        $sql = "SELECT ";
        foreach ($columns as $column) {
            $sql .= "$column,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " FROM $table";
        foreach ($join as $joinTable => $joinOn) {
            $sql .= " JOIN $joinTable ON $joinOn";
        }
        if (!empty($where)) {
            $sql .= " WHERE ";
            foreach ($where as $column => $value) {
                $sql .= "$column = ? AND ";
            }
            $sql = substr($sql, 0, -5);
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        $stmt = (new Database)->prepare($sql);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * This function is used to get a single row from the database
     * 
     * Examples:
     * Database::get('users', ['name', 'email'], [], ['id' => 1]);
     *  This will return the name and email of the user with id 1
     * Database::get('users', ['name', 'email'], [], ['id' => 1], 'name DESC');
     *  This will return the name and email of the user with id 1 ordered by name in descending order
     * Databse::get('users', ['name', 'email'], ['user_roles' => 'users.role_id = user_roles.id'], ['users.id' => 1]);
     *  This will return the name and email of the user with id 1 with a join on user_roles table
     * 
     * @param string $table
     * @param array $columns
     * @param $join
     * @param array $where
     * @param string $orderBy
     * @return mixed
     */
    public static function get(string $table, array $columns = ['*'],$join = [], array $where = [], string $orderBy = '')
    {
        $sql = "SELECT ";
        foreach ($columns as $column) {
            $sql .= "$column,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " FROM $table";
        foreach ($join as $joinTable => $joinOn) {
            $sql .= " JOIN $joinTable ON $joinOn";
        }
        if (!empty($where)) {
            $sql .= " WHERE ";
            foreach ($where as $column => $value) {
                $sql .= "$column = ? AND ";
            }
            $sql = substr($sql, 0, -5);
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        $stmt = (new Database)->prepare($sql);
        $stmt->execute(array_values($where));
        return $stmt->fetchobject();
    }

    /**
     * This function is used to update data in the database
     * 
     * Example:
     * Database::update('users', ['name', 'email'], ['John Doe', 'john.doe@gmail.com'], ['id' => 1]);
     * 
     * This will update the name and email of the user with id 1
     * 
     * @param string $table
     * @param array $columns
     * @param array $values
     * @param array $where
     * @param Database $database
     * @return void
     */
    public static function update(string $table, array $columns, array $values, array $where, Database $database = new Database)
    {
        $sql = "UPDATE $table SET ";
        foreach ($columns as $column) {
            $sql .= "$column = ?,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE ";
        foreach ($where as $column => $value) {
            $sql .= "$column = ? AND ";
        }
        $sql = substr($sql, 0, -5);
        $sql = htmlspecialchars($sql);
        $stmt = $database->prepare($sql);
        $stmt->execute(array_merge($values, array_values($where)));
    }

    /**
     * This function is used to delete data from the database
     * 
     * Example:
     * Database::delete('users', ['id' => 1]);
     * 
     * This will delete the user with id 1
     *  
     * @param string $table
     * @param array $where
     * @return void
     */
    public static function delete(string $table, array $where, Database $database = new Database)
    {
        $sql = "DELETE FROM $table WHERE ";
        foreach ($where as $column => $value) {
            $sql .= "$column = ? AND ";
        }
        $sql = substr($sql, 0, -5);
        $sql = htmlspecialchars($sql);
        $stmt = $database->prepare($sql);
        $stmt->execute(array_values($where));
    }

    /**
     * This function is used to run custom queries
     * 
     * Example:
     * $users = Database::query("SELECT * FROM users WHERE id = ?", [1]);
     * 
     * This will return all users with id 1
     * 
     * Note: If you want to use more functions of pdo before or after the query, you can pass the database object in $database
     * 
     * @param string $query
     * @param array $values
     * @param Database $database
     * @return mixed
     */
    public static function query(string $query, array $values = [], Database $database = new Database)
    {
        $stmt = $database->prepare($query);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * This function is used to begin a transaction
     * A transaction is a set of queries that are executed together by using Database::commit($database)
     * If one query fails, the entire transaction can be rolled back by using Database::rollBack($database)
     * 
     * Example:
     * $database = Database::beginTransaction();
     * 
     * This will begin a transaction
     * 
     * @return Database
     */
    public static function beginTransaction()
    {
        $database = new Database;
        $database->connection->beginTransaction();

        return $database;
    }

    /**
     * This function is used to commit a transaction
     * A transaction is a set of queries that are executed together by using Database::commit($database)
     * If one query fails, the entire transaction can be rolled back by using Database::rollBack($database)
     * 
     * Example:
     * Database::commit($database);
     * 
     * This will commit the transaction
     * 
     * @param Database $database
     * @return void
     */
    public static function commit(Database $database)
    {
        $database->connection->commit();
    }

    /**
     * This function is used to roll back a transaction
     * A transaction is a set of queries that are executed together by using Database::commit($database)
     * If one query fails, the entire transaction can be rolled back by using Database::rollBack($database)
     * 
     * Example:
     * Database::rollBack($database);
     * 
     * This will roll back the transaction
     * 
     * @param Database $database
     * @return void
     */
    public static function rollBack(Database $database)
    {
        $database->connection->rollBack();
    }
}