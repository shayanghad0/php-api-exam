<?php
include "auth.php";

$error = "";

// Login with token (in URL)
if (isset($_GET["token"])) {
    $user = findUserByToken($_GET["token"]);
    if ($user) {
        $_SESSION["user"] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid or expired token.";
    }
}

// Login with form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = findUser($_POST["username"], $_POST["password"]);
    if ($user) {
        $remember = isset($_POST["remember"]) && $_POST["remember"] == "1";
        assignNewToken($user["username"], $remember);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    Username: <input name="username" required><br>
    Password: <input name="password" type="password" required><br>
    Remember Me: <input type="checkbox" name="remember" value="1"><br>
    <button>Login</button>
</form>
</body>
</html>
