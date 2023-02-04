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
        if (!isset($account['password'])) {
            return '<script>alert("Wrong username or password")</script>';
        } elseif (password_verify($password, $account['password'])) {
            if ($account['admin'] == 1) {
                $_SESSION['admin'] = $account['id'];
            }
            $_SESSION['access'] = $account['id'];
        } else {
            return '<script>alert("Wrong username or password")</script>';
        }
    }

    public static function update($id, $password, $username, $admin){
        database::update('account', $id, ['password', 'username', 'admin'], 'sss', [$password, $username, $admin]);
    }

    public static function sdelete($id){
        database::update('account', $id, ['removed'], 's', [1]);
    }
}
