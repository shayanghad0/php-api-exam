<?php
include "auth.php";
if (isset($_SESSION["user"])) {
    clearToken($_SESSION["user"]["username"]);
}
session_destroy();
header("Location: index.php");
exit();
