<?php

class DatabaseUtilities {
    public static function getAllowedColumns($queryTables = array(), $allowedColumns = array()) {
        if (count($queryTables)) {
            foreach ($queryTables as $table) {
                $query = "SHOW COLUMNS FROM " . $table;
                $result = database::executeQuery($query);

                foreach (database::fetch($result['stmt']) as $row) {
                    $allowedColumns[] = $table . '.' . $row['Field'];
                    $allowedColumns[] = $row['Field'];
                }
            }
        }
        return $allowedColumns;
    }

    public static function getAllowedFilters($allowedFilters = array()) {
        $allowedFilters[] = '=';
        $allowedFilters[] = '!=';
        $allowedFilters[] = '>';
        $allowedFilters[] = '>=';
        $allowedFilters[] = '<';
        $allowedFilters[] = '<=';
        $allowedFilters[] = 'IS NULL';
        $allowedFilters[] = 'IS NOT NULL';

        return $allowedFilters;
    }

    public static function getAllowedSortDirections($allowedSortDirections = array()) {
        $allowedSortDirections[] = 'ASC';
        $allowedSortDirections[] = 'DESC';
        return $allowedSortDirections;
    }

    public static function generateWhereClause($filterArray = array(), $allowedColumns, $allowedFilters, $customFilters = array()) {
        global $returnErrors;

        $filterQueries = array();
        $filterValues = array();
        $filterTypes = '';

        if (count($filterArray)) {
            foreach ($filterArray as $filter) {
                if (
                    in_array($filter['property'], $allowedColumns) &&
                    in_array($filter['comparisonType'], $allowedFilters)
                ) {
                    if (
                    array_key_exists($filter['property'], $customFilters)
                    ) {
                        $customFilter = $customFilters[$filter['property']];

                        if (count($filter['value']) == 1) {
                            $filterQueries[] = $customFilter['statement'];
                            for ($i = 0; $i < $customFilter['variableOccurances']; $i++) {
                                $filterValues[] = $filter['value'];
                                $filterTypes .= $filter['type'];
                            }
                        } else {
                            $subfilterQuery = array();
                            foreach ($filter['value'] as $filterValue) {
                                $subfilterQuery[] = $customFilter['statement'];
                                for ($i = 0; $i < $customFilter['variableOccurances']; $i++) {
                                    $filterValues[] = $filterValue;
                                    $filterTypes .= $filter['type'];
                                }
                            }
                            $filterQueries[] = '(' . implode(' OR ', $subfilterQuery) . ')';
                        }
                    } elseif (count($filter['value']) == 1) {
                        $filterQueries[] = $filter['property'] . ' ' . $filter['comparisonType'] . ' ?';
                        $filterValues[] = $filter['value'];
                        $filterTypes .= $filter['type'];
                    } else {
                        $subfilterQuery = array();
                        foreach ($filter['value'] as $filterValue) {
                            $subfilterQuery[] = $filter['property'] . ' ' . $filter['comparisonType'] . ' ?';
                            $filterValues[] = $filterValue;
                            $filterTypes .= $filter['type'];
                        }

                        $filterQueries[] = '(' . implode(' OR ', $subfilterQuery) . ')';
                    }
                } else {
                    $returnErrors[] = 'Filter property or comparisonType is not allowed: ' . $filter['property'] . ', ' . $filter['comparisonType'];
                }
            }
        }

        if (count($filterQueries) === 0) {
            return false;
        } else {
            $customQuery = implode(' AND ', $filterQueries);

            return array(
                'customQuery' => $customQuery,
                'values' => $filterValues,
                'types' => $filterTypes
            );
        }
    }

    public static function generateOrderByClause($sortItemsArray, $allowedColumns, $allowedSortDirections) {
        $sortItems = array();

        foreach ($sortItemsArray as $sortItemArray) {
            if (
                in_array($sortItemArray['property'], $allowedColumns) &&
                (
                in_array($sortItemArray['direction'], $allowedSortDirections)
                )
            ) {
                $sortItems[] = '' . $sortItemArray['property'] . ' ' . $sortItemArray['direction'];
            } else {
                $returnErrors[] = 'Sort property or direction is not allowed: ' . $sortItemArray['property'] . ', ' . $sortItemArray['direction'];
            }
        }

        if (count($sortItems)) {
            return ' ORDER BY ' . implode(',', $sortItems);
        } else {
            return '';
        }

    }
}
