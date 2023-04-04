<?php

/**
 * Database
 */
class Database {

    /**
     * Connect to the database
     *ef
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @return void
     */
    public static function connect($host, $user, $password, $database) {
        global $mysqli;
        @$mysqli = new mysqli($host, $user, $password, $database);
        if ($mysqli->connect_errno > 0) {
            die(sprintf("Cannot connect to database! <br />%s", $mysqli->connect_error));
        }
    }

    /**
     * Execute the query
     *
     * @param $query
     * @param $types
     * @param $values
     * @return array
     */
    public static function executeQuery($query, $types = null, $values = null) {
        global $mysqli;
        if ($query == '') {
            return [
                'success' => false,
                'stage'   => 'start',
                'query'   => $query,
                'error'   => 'no query provided'
            ];
        }
        if ($stmt = $mysqli->prepare($query)) {
            if ($values !== null && count($values)) {
                $types    = [$types];
                $params   = array_merge($types, $values);
                $tmpArray = [];
                foreach ($params as $i => $value) {
                    $tmpArray[$i] = &$params[$i];
                }
                call_user_func_array([$stmt, 'bind_param'], $tmpArray);
            }
            if (!$stmt->execute()) {
                return [
                    'success' => false,
                    'stage'   => 'query execution',
                    'query'   => $query,
                    'error'   => $mysqli->error
                ];
            } else {
                $stmt->store_result();
                @mysqli_next_result($mysqli);
                return [
                    'success' => true,
                    'stmt'    => $stmt
                ];
            }
        } else {
            return [
                'success' => false,
                'stage'   => 'query preparation',
                'query'   => $query,
                'error'   => $mysqli->error
            ];
        }
    }

    /**
     * Fetch the results
     *
     * @param $result
     * @return array
     */
    public static function fetch($result) {
        $array = [];
        if ($result instanceof mysqli_stmt) {
            $variables = [];
            $data      = [];
            $meta      = $result->result_metadata();
            while ($field = $meta->fetch_field()) {
                $variables[] = &$data[$field->name];
            }
            call_user_func_array([$result, 'bind_result'], $variables);
            $i = 0;
            while ($result->fetch()) {
                $array[$i] = [];
                foreach ($data as $k => $v) {
                    $array[$i][$k] = $v;
                }
                $i++;
            }
        } elseif ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        }
        return $array;
    }

    /**
     * Fetch the row
     *
     * @param $result
     * @return false|mixed
     */
    public static function fetchRow($result) {
        $results = self::fetch($result);
        if (count($results)) {
            return $results[0];
        } else {
            return false;
        }
    }

    /**
     * Add a record to the database
     *
     * @param $table
     * @param $fields
     * @param $types
     * @param $values
     * @return array
     */
    public static function add($table, $fields, $types, $values) {
        $fieldQueries = [];
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
        }
        $fieldQuery = implode(',', $fieldQueries);
        $query      = "INSERT INTO " . $table . " SET " . $fieldQuery;
        return self::executeQuery($query, $types, $values);
    }

    /**
     * Update a row in the database
     *
     * @param $table
     * @param $id
     * @param $fields
     * @param $types
     * @param $values
     * @return array
     */
    public static function update($table, $id, $fields, $types, $values) {
        $fieldQueries = [];
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
        }
        $fieldQuery = implode(',', $fieldQueries);
        $types      .= 'i';
        $values[]   = $id;
        $query      = "UPDATE " . $table . " SET " . $fieldQuery . " WHERE id = ?";
        return self::executeQuery($query, $types, $values);
    }

    /**
     * Get a single row from the database
     *
     * @param $table
     * @param $fields
     * @param $types
     * @param $values
     * @param $orderBy
     * @return array|false|mixed
     */
    public static function getRow($table, $fields = false, $types = false, $values = false, $orderBy = '') {
        if ($fields === false || $types === false || $values === false) {
            $types      = null;
            $values     = null;
            $fieldQuery = '1 = 1';
        } else {
            $fieldQueries = [];
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }
        $orderByQuery = "";
        if ($orderBy) {
            $orderByQuery = "ORDER BY " . $orderBy;
        }
        $query  = "SELECT * FROM " . $table . " WHERE " . $fieldQuery . " " . $orderByQuery;
        $result = self::executeQuery($query, $types, $values);
        if ($result['success']) {
            return self::fetchRow($result['stmt']);
        } else {
            return $result;
        }
    }

    /**
     * Get all rows from the database
     *
     * @param $table
     * @param $fields
     * @param $types
     * @param $values
     * @param $orderBy
     * @return array
     */
    public static function getRows($table, $fields = false, $types = false, $values = false, $orderBy = false) {
        if ($fields === false || $types === false || $values === false) {
            $types      = null;
            $values     = null;
            $fieldQuery = '1 = 1';
        } else {
            $fieldQueries = [];
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }
        $orderByQuery = "";
        if ($orderBy) {
            $orderByQuery = " ORDER BY " . $orderBy;
        }
        $query  = "SELECT * FROM " . $table . " WHERE " . $fieldQuery . $orderByQuery;
        $result = self::executeQuery($query, $types, $values);
        if ($result['success']) {
            return self::fetch($result['stmt']);
        } else {
            return $result;
        }
    }

    /**
     * Update a row in the database
     *
     * @param $table
     * @param $id
     * @param $fields
     * @param $types
     * @param $values
     * @return array
     */
    public static function delete($table, $id, $fields = false, $types = false, $values = false)
    {
        $fieldQueries = [];
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
        }
        $fieldQuery = implode(',', $fieldQueries);
        $types      .= 'i';
        $values[]   = $id;
        $query      = "delete from " . $table . " WHERE id = ?";
        return self::executeQuery($query, $types, $values);
    }
}
