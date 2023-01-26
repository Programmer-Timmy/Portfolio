<?php
class accounts {
    public static function Login($password, $username)
    {
        $account = database::getRow('account', ['username'], 's', [$username]);
        if (!isset($account['password'])) {
            return '<script>alert("Wrong username or password")</script>';
        } elseif (password_verify($password, $account['password'])) {
            $_SESSION['access'] = "logged";
        } else {
            return '<script>alert("Wrong username or password")</script>';
        }
    }
    
}

?>
