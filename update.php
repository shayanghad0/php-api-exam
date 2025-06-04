<?php
include "auth.php";
checkLogin();

$users = getUsers();
foreach ($users as &$u) {
    if ($u["username"] === $_SESSION["user"]["username"]) {
        $u["name"] = $_POST["name"];
        $u["username"] = $_POST["username"];
        if (!empty($_POST["password"])) {
            $u["password"] = $_POST["password"];
        }
        $u["token"] = generateToken();
        $u["token_time"] = time();
        $_SESSION["user"] = $u;
        break;
    }
}
saveUsers($users);
header("Location: dashboard.php");
exit();
