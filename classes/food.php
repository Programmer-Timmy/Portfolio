<?php

/**
 * Food
 */
class Food {

    /**
     * get the menu for the next 14 days
     *
     * @return mixed
     */
    public function getMenu() {
        global $mysqli;
        $results = $mysqli->query("SELECT * FROM `menu` WHERE CAST(`date` AS DATE) BETWEEN '" . date("Y-m-d") . "' AND '" . date('Y-m-d', strtotime('+14 days')) . "'");
        return $results;
    }

    /**
     * get the menu from the database
     *
     * @param $id
     * @return array|false|mixed
     */
    public function loadMenu($id = 0) {
        if ($id == 0) {
            return false;
        }
        $results = database::getRow('menu', ['id', 'deleted'], 'ii', [$id, 0]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * load menu from today
     *
     * @return array|false|mixed
     */
    public function loadMenuToday() {
        $results = database::getRow('menu', ['date', 'deleted'], 'si', [date('Y-m-d'), 0]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * load a reservation
     *
     * @param $id
     * @return array
     */
    public function loadReservations($id) {
        $results = database::getRows('reservations', ['menu_id', 'deleted'], 'ii', [$id, 0]);
        if (count($results) >= 1 and $results != false) {
            return $results;
        } else {
            return [];
        }
    }


    /**
     * Add to the menu
     *
     * @param $data
     * @return bool
     */
    public function addToMenu($data) {
        $res = $this->save(
            'menu',
            [
                'name',
                'descr',
                'date'
            ],
            'sss',
            [
                $data['name'],
                $data['descr'],
                $data['date'],
            ]
        );
        if ($res === false) {
            return false;
        } else {
            if ($res['success']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Try and signup a user
     *
     * @param $user
     * @param $menu
     * @return bool
     */
    public function userSignUp($user, $menu) {
        $res = $this->save(
            'reservations',
            ['user_id', 'menu_id', 'fully_paid', 'amount_left'],
            'iiis',
            [
                $user,
                $menu,
                0,
                '3.5000'
            ]
        );
        if ($res === false) {
            return false;
        } else {
            if ($res['success']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Try and signoff a user
     *
     * @param $user
     * @param $menu
     * @return bool
     */
    public function userSignOff($user, $menu) {
        $results = database::getRow('reservations', ['menu_id', 'user_id', 'deleted'], 'iii', [$menu, $user, 0]);
        if (!$results) {
            return false;
        } else {
            $res = $this->save(
                'reservations',
                ['deleted'],
                'i',
                [
                    1,
                ],
                $results['id']
            );
            if ($res === false) {
                return false;
            } elseif ($res['success']) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Payment of a user
     *
     * @param $user
     * @param $menu
     * @return bool
     */
    public function userPayment($user, $menu) {
        $results = database::getRow('reservations', ['menu_id', 'user_id', 'deleted'], 'iii', [$menu, $user, 0]);
        if (!$results) {
            return false;
        } else {
            $res = $this->save(
                'reservations',
                [
                    'fully_paid',
                    'amount_left'
                ],
                'is',
                [
                    1,
                    '0.0000'
                ],
                $results['id']
            );
            if ($res === false) {
                return false;
            } elseif ($res['success']) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check if a user has signed up
     *
     * @param $menu
     * @param $user
     * @return bool
     */
    public function hasUserSignedUp($menu = 0, $user = 0) {
        if ($menu == 0 || $user == 0) {
            return false;
        }
        $results = database::getRow('reservations', ['menu_id', 'user_id', 'deleted'], 'iii', [$menu, $user, 0]);
        if ($results) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a user has fully paid the meal
     *
     * @param $menu
     * @param $user
     * @return bool
     */
    public function hasUserFullyPaid($menu = 0, $user = 0) {
        if ($menu == 0 || $user == 0) {
            return false;
        }
        $results = database::getRow('reservations', ['menu_id', 'user_id', 'deleted'], 'iii', [$menu, $user, 0]);
        if ($results['fully_paid'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get amount left, if there is any.
     *
     * @param $menu
     * @param $user
     * @return false|mixed
     */
    public function getAmountLeft($menu = 0, $user = 0) {
        if ($menu == 0 || $user == 0) {
            return false;
        }
        $results = database::getRow('reservations', ['menu_id', 'user_id', 'deleted'], 'iii', [$menu, $user, 0]);
        return $results['amount_left'];
    }

    /**
     * Saves OR updates data to the server.
     *
     * @param string|null $table
     * @param array|null  $fields
     * @param string|null $types
     * @param array|null  $values
     * @param int|null    $update
     * @return array|false
     */
    public function save($table = null, $fields = null, $types = null, $values = null, $update = null) {
        if ($table == null || $fields == null || $types == null || $values == null) {
            return false;
        }
        if ($update != null) {
            return database::update($table, $update, $fields, $types, $values);
        } else {
            return database::add($table, $fields, $types, $values);
        }
    }
}
