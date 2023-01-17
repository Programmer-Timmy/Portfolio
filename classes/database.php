<?php

class Database {

    public static function connect($host, $user, $password, $database) {
        global $mysqli;
        @$mysqli = new mysqli($host, $user, $password, $database);
        if ($mysqli->connect_errno > 0)
            die('Cannot connect to database! ' . $mysqli->connect_error);
    }

    public static function executeQuery($query, $types = null, $values = null) {
        global $mysqli;

        if ($query == '') {
            return array(
                'success' => false,
                'stage' => 'start',
                'query' => $query,
                'error' => 'no query provided'
            );
            //die('Empty query!');
        }

        if ($stmt = $mysqli->prepare($query)) {

            if ($values !== null && count($values)) {
                $types = array($types);
                $params = array_merge($types, $values);

                $tmpArray = array();
                foreach ($params as $i => $value) {
                    $tmpArray[$i] = &$params[$i];
                }

                call_user_func_array(array($stmt, 'bind_param'), $tmpArray);
            }

            if (!$stmt->execute()) {
                return array(
                    'success' => false,
                    'stage' => 'query execution',
                    'query' => $query,
                    'error' => $mysqli->error
                );
                //die('Query execution failed: '.$stmt->error.' in query: '.$query);
            } else {
                $stmt->store_result();

                return array(
                    'success' => true,
                    'stmt' => $stmt
                );
            }
        } else {
            return array(
                'success' => false,
                'stage' => 'query preparation',
                'query' => $query,
                'error' => $mysqli->error
            );
            //die('There is an error in your query "'.$query.'". Error message: '.);
        }
    }

    public static function fetch($result) {
        $array = array();

        if ($result instanceof mysqli_stmt) {
            $result->store_result();

            $variables = array();
            $data = array();
            $meta = $result->result_metadata();

            while ($field = $meta->fetch_field()) {
                $variables[] = &$data[$field->name];
            }

            call_user_func_array(array($result, 'bind_result'), $variables);

            $i = 0;
            while ($result->fetch()) {
                $array[$i] = array();
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

    public static function fetchRow($result) {
        $results = self::fetch($result);

        if (count($results)) {
            return $results[0];
        } else {
            return false;
        }
    }

    public static function add($table, $fields, $types, $values) {
        $fieldQueries = array();
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
        }
        $fieldQuery = implode(',', $fieldQueries);

        $query = "INSERT INTO " . $table . " SET " . $fieldQuery;

        $result = self::executeQuery($query, $types, $values);
        return $result;
    }

    public static function update($table, $id, $fields, $types, $values) {
        $fieldQueries = array();
        foreach ($fields as $field) {
            $fieldQueries[] = $field . ' = ?';
        }
        $fieldQuery = implode(',', $fieldQueries);

        $types .= 'i';
        $values[] = $id;

        $query = "UPDATE " . $table . " SET " . $fieldQuery . " WHERE id = ?";

        $result = self::executeQuery($query, $types, $values);
        return $result;
    }

    public static function getRow($table, $fields = false, $types = false, $values = false, $orderBy = '') {

        if (
            $fields === false ||
            $types === false ||
            $values === false
        ) {
            $types = null;
            $values = null;
            $fieldQuery = '1 = 1';
        } else {
            $fieldQueries = array();
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }

        $query = "SELECT * FROM " . $table . " WHERE " . $fieldQuery . "" . $orderBy;
        $result = self::executeQuery($query, $types, $values);

        if ($result['success']) {
            return self::fetchRow($result['stmt']);
        } else {
            return $result;
        }
    }

    public static function getRows($table, $fields = false, $types = false, $values = false, $orderBy = false) {

        if (
            $fields === false ||
            $types === false ||
            $values === false
        ) {
            $types = null;
            $values = null;
            $fieldQuery = '1 = 1';
        } else {
            $fieldQueries = array();
            foreach ($fields as $field) {
                $fieldQueries[] = $field . ' = ?';
            }
            $fieldQuery = implode(' AND ', $fieldQueries);
        }

        $orderByQuery = "";
        if ($orderBy) {
            $orderByQuery = " ORDER BY " . $orderBy;
        }

        $query = "SELECT * FROM " . $table . " WHERE " . $fieldQuery . $orderByQuery;
        $result = self::executeQuery($query, $types, $values);

        if ($result['success']) {
            return self::fetch($result['stmt']);
        } else {
            return $result;
        }
    }
}
