<?php
/**
 * accouts
 */
class accounts
{

    /**
     * @function
     * loading the accounts that are not deleted
     * @return array/false
     */
    public static function loadaccounts()
    {
        $results = database::getRows('account', ['removed'], 's', [0]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * @function
     * loading 1 account
     * @param $id
     * @return array/false
     */
    public static function loadaccount($id = 0)
    {
        if ($id == 0) {
            return false;
        }
        $results = database::getRow('account', ['id'], 's', [$id]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * @function
     * login
     * @param $password
     * @param $username
     * @return string|void
     */
    public static function Login($password, $username){
        $account = database::getRow('account', ['username', 'removed'], 'ss', [$username, 0]);
        if ($account['attempts'] < 3){
            if (!isset($account['password'])) {
                return '<script>alert("Wrong username or password")</script>';
                
            } elseif (password_verify($password, $account['password'])) {
                if ($account['admin'] == 1) {
                    $_SESSION['admin'] = $account['id'];
                }
                database::update('account', $account['id'], ['attempts'], 's', [0]);
                $_SESSION['access'] = $account['id'];
                $_SESSION['discard_after'] = time() + 1800;
                
            } else {
                $attempts = $account['attempts'];
                $attempts++;
                database::update('account', $account['id'], ['attempts'], 's', [$attempts]);
                return '<script>alert("Wrong username or password")</script>';
            }
        } else {
            return '<script>alert("Too many attempts, Contact a admin!")</script>';
        }
    }

    /**
     * @function
     * adding a account
     * @param $password
     * @param $username
     * @param $admin
     * @return string
     */
    public static function add($password, $username, $admin){
        if ($password !== " "){
        $hashpw = password_hash($password, PASSWORD_DEFAULT);
        }
        $retrun = database::add('account', ['password', 'username', 'admin'], 'sss', [$hashpw, $username, isset($admin)? 1 : 0]);
        if($retrun['success']) {
            return '<script>if(confirm("Account toegevoegt")) document.location = "users";</script>';
        } else {
            return '<script>alert("Toevoegen niet gelukt")</script>';
        }
    }

    /**
     * @function
     * updating account
     * @param $id
     * @param $password
     * @param $username
     * @param $admin
     */
    public static function update($id, $password, $username, $admin){
        if ($_POST["password"] == "") {
            $result = accounts::loadaccount($id);
            $password = $result['password'];
        }else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        database::update('account', $id, ['password', 'username', 'admin'], 'sss', [$password, $username, $admin]);
    }

    /**
     * @function
     * soft delete account
     * @param $id
     */
    public static function sdelete($id){
        database::update('account', $id, ['removed'], 's', [1]);
    }

    public static function delete(){
        $results = database::getRows('account', ['removed'], 's', [1]);
        foreach($results as $result){
                $date = date_create($result['updated']);
                date_add($date, date_interval_create_from_date_string("6 day"));
                $max_date = date_format($date, "y-m-d");
                if($max_date <= date("y-m-d")){
                    echo Database::delete('account', $result['id']);
                }

        }

    }
}
