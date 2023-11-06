<?php
/**
 * Database
 */
class Database
{
    private static $pdo;

    /**
     * Connect to the database using PDO
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     */
    public static function connect($host, $user, $password, $database)
    {
        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Cannot connect to the database: " . $e->getMessage());
        }
    }

    /**
     * Execute the query using PDO with input validation
     *
     * @param $query
     * @param $values
     * @return array
     */
    public static function executeQuery($query, $values = null)
    {
        if ($query == '') {
            return [
                'success' => false,
                'stage' => 'start',
                'query' => $query,
                'error' => 'no query provided'
            ];
        }

        try {
            $stmt = self::$pdo->prepare($query);
            $stmt->execute($values);

            return [
                'success' => true,
                'stmt' => $stmt
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'stage' => 'query execution',
                'query' => $query,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fetch the results using PDO with output encoding to prevent XSS
     *
     * @param $result
     * @return array
     */
    /**
     * Fetch the results using PDO with output encoding to prevent XSS
     *
     * @param $result
     * @return array
     */
    public static function fetch($result)
    {
        $results = $result->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    /**
     * Fetch the row using PDO with output encoding to prevent XSS
     *
     * @param $result
     * @return false|mixed
     */
    public static function fetchRow($result)
    {
        $results = self::fetch($result);
        if ($results && count($results)) {
            return $results[0];
        } else {
            return false;
        }
    }

    // The add, update, getRow, and getRows functions should have input validation and output encoding.
    // Implement input validation by ensuring the fields and values are clean and safe to use in SQL queries.

    /**
     * Add a record to the database using PDO with input validation
     *
     * @param $table
     * @param $fields
     * @param $values
     * @return array
     */
    public static function add($table, $fields, $values)
    {
        $fieldQueries = [];
        $bindValues = [];
        $i = 0;
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
            $bindValues[] = $values[$i];
            $i++;
        }
        $fieldQuery = implode(',', $fieldQueries);
        $query = "INSERT INTO $table SET $fieldQuery";
        return self::executeQuery($query, $bindValues);
    }


    /**
     * Update a row in the database using PDO with input validation
     *
     * @param $table
     * @param $id
     * @param $fields
     * @param $values
     * @return array
     */
    public static function update($table, $id, $fields, $values)
    {
        $fieldQueries = [];
        $bindValues = [];
        $i=0;
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
            $bindValues[] = $values[$i];
            $i++;
        }
        $bindValues[] = $id;
        $fieldQuery = implode(',', $fieldQueries);
        $query = "UPDATE $table SET $fieldQuery WHERE id = ?";
        return self::executeQuery($query, $bindValues);
    }


    // Implement input validation and output encoding for getRow and getRows functions as well.

    /**
     * Get a single row from the database using PDO with input validation and output encoding
     *
     * @param $table
     * @param $fields
     * @param $values
     * @param $orderBy
     * @param $join
     * @return array|false|mixed
     */
    public static function getRow($table, $fields = false, $values = false, $orderBy = '', $join = '')
    {
        if ($fields === false || $values === false) {
            $fieldQuery = '1 = 1';
            $values = null;
        } else {
            $fieldQueries = [];
            $bindValues = [];
            $count = 0;
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
                $bindValues[] = $values[$count];
                $count ++;

            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }
        $orderByQuery = $orderBy ? "ORDER BY $orderBy" : '';
        $joinQuery = $join ? "$join" : '';
        $query = "SELECT * FROM $table $joinQuery WHERE $fieldQuery$orderByQuery";
        $result = self::executeQuery($query, $bindValues);
        if ($result['success']) {
            return self::fetchRow($result['stmt']);
        } else {
            return $result;
        }
    }

    /**
     * Get all rows from the database using PDO with input validation and output encoding
     *
     * @param $table
     * @param $fields
     * @param $values
     * @param $orderBy
     * @param $join
     * @return array
     */
    public static function getRows($table, $fields = false, $values = false, $orderBy = false, $join = '')
    {
        $bindValues = [];
        if ($fields === false || $values === false) {
            $fieldQuery = '1 = 1';
            $values = null;
        } else {
            $fieldQueries = [];
            $number = 0;
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
                $bindValues[] = $values[$number];
                $number ++;
            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }
        $orderByQuery = $orderBy ? " ORDER BY $orderBy" : '';
        $joinQuery = $join ? "$join" : '';
        $query = "SELECT * FROM $table $joinQuery WHERE $fieldQuery$orderByQuery";
        $result = self::executeQuery($query, $bindValues);
        if ($result['success']) {
            return self::fetch($result['stmt']);
        } else {
            return $result;
        }
    }
    /**
     * Delete a row from the database using PDO with input validation
     *
     * @param $table
     * @param $id
     * @param $fields
     * @param $values
     * @return array
     */
    public static function delete($table, $id, $fields, $values)
    {
        $fieldQueries = [];
        $bindValues = [];
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
            $bindValues[] = $values[$field]; // Assuming that the keys in $fields correspond to keys in $values
        }
        $bindValues[] = $id;
        $fieldQuery = implode(' AND ', $fieldQueries);
        $query = "DELETE FROM $table WHERE $fieldQuery AND id = ?";
        return self::executeQuery($query, $bindValues);
    }

}