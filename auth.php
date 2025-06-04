<?php
session_start();

function getUsers() {
    return json_decode(file_get_contents("log.json"), true);
}

function saveUsers($users) {
    file_put_contents("log.json", json_encode($users, JSON_PRETTY_PRINT));
}

function findUser($username, $password) {
    foreach (getUsers() as $user) {
        if ($user["username"] === $username && $user["password"] === $password) {
            return $user;
        }
    }
    return null;
}

function findUserByToken($token) {
    $now = time();
    foreach (getUsers() as $user) {
        if (
            isset($user["token"], $user["token_time"], $user["token_expiry"]) &&
            $user["token"] === $token &&
            $now - $user["token_time"] <= $user["token_expiry"]
        ) {
            return $user;
        }
    }
    return null;
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function assignNewToken($username, $remember = false) {
    $users = getUsers();
    foreach ($users as &$u) {
        if ($u["username"] === $username) {
            $u["token"] = generateToken();
            $u["token_time"] = time();
            $u["token_expiry"] = $remember ? 604800 : 600; // 7 days or 10 mins
            $_SESSION["user"] = $u;
            saveUsers($users);
            return $u["token"];
        }
    }
    return null;
}

function clearToken($username) {
    $users = getUsers();
    foreach ($users as &$u) {
        if ($u["username"] === $username) {
            $u["token"] = null;
            $u["token_time"] = null;
            $u["token_expiry"] = null;
            break;
        }
    }
    saveUsers($users);
}

function checkLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit();
    }
}
?>
