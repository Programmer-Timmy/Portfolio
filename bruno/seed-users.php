<?php
/**
 * Dev-only seeder for the Bruno API test collection.
 *
 *   php bruno/seed-users.php          create / update the test users
 *   php bruno/seed-users.php --down   remove them
 *
 * Creates:
 *   bruno-admin   (admin = 1)   password: bruno-dev-pw
 *   bruno-user    (admin = 0)   password: bruno-dev-pw
 */

// settings.php reads $_SERVER['REQUEST_URI']; give it something under CLI.
$_SERVER['REQUEST_URI'] ??= '/';

$root = dirname(__DIR__);
require $root . '/private/config/settings.php';
require $root . '/private/controllers/Database.php';

const PASSWORD = 'bruno-dev-pw';
$users = [
    ['username' => 'bruno-admin', 'admin' => 1],
    ['username' => 'bruno-user', 'admin' => 0],
];

$down = in_array('--down', $argv, true);

if ($down) {
    foreach ($users as $u) {
        Database::query('DELETE FROM users WHERE username = ?', [$u['username']]);
        echo "removed {$u['username']}\n";
    }
    exit(0);
}

$hash = password_hash(PASSWORD, PASSWORD_DEFAULT);

foreach ($users as $u) {
    $existing = Database::get('users', ['id'], [], ['username' => $u['username']]);
    if ($existing) {
        Database::update('users', ['password_hash', 'admin'], [$hash, $u['admin']], ['id' => $existing->id]);
        echo "updated {$u['username']} (admin={$u['admin']})\n";
    } else {
        Database::insert('users', ['username', 'password_hash', 'admin'], [$u['username'], $hash, $u['admin']]);
        echo "created {$u['username']} (admin={$u['admin']})\n";
    }
}

echo "password: " . PASSWORD . "\n";
