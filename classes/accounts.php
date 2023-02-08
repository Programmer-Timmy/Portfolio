<?php
class accounts
{
    public static function loadaccounts()
    {
        $results = database::getRows('account');
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

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

    public static function Login($password, $username){
        $account = database::getRow('account', ['username'], 's', [$username]);
        if ($account['attempts'] > 3){
            if (!isset($account['password'])) {
                return '<script>alert("Wrong username or password")</script>';
                
            } elseif (password_verify($password, $account['password'])) {
                if ($account['admin'] == 1) {
                    $_SESSION['admin'] = $account['id'];
                }
                $_SESSION['access'] = $account['id'];
            } else {
                return '<script>alert("Wrong username or password")</script>';
                database::update('account', $account['id'], ['attempts'], 's', [$account['attempts']++]);
            }
        } else {
            return '<script>alert("Too many attempts, Contact a admin!")</script>';
        }
    }

    public static function add($password, $username, $admin){
        if ($password !== " "){
        $hashpw = password_hash($password, PASSWORD_DEFAULT);
        }
        $retrun = database::add('account', ['password', 'username', 'admin'], 'sss', [$hashpw, $username, $admin]);
        if($retrun['success']) {
            return '<script>if(confirm("Account toegevoegt")) document.location = "users";</script>';
        } else {
            return '<script>alert("Toevoegen niet gelukt")</script>';
        }
    }

    public static function update($id, $password, $username, $admin){
        if ($_POST["password"] == "") {
            $result = accounts::loadaccount($id);
            $password = $result['password'];
        }else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        database::update('account', $id, ['password', 'username', 'admin'], 'sss', [$password, $username, $admin]);
    }

    public static function sdelete($id){
        database::update('account', $id, ['removed'], 's', [1]);
    }
}
