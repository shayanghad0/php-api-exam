<?php
include "auth.php";
checkLogin();
$user = $_SESSION["user"];

$expirySeconds = $user["token_expiry"] ?? 600;
$expires = date("Y-m-d H:i:s", $user["token_time"] + $expirySeconds);
?>

<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
<h2>Welcome, <?= htmlspecialchars($user["name"]) ?></h2>
<p><strong>Username:</strong> <?= htmlspecialchars($user["username"]) ?></p>
<p><strong>Token (expires at <?= $expires ?>):</strong><br>
<code><?= htmlspecialchars($user["token"]) ?></code></p>

<p><strong>Login via URL:</strong><br>
<code>http://localhost:800/index.php?token=<?= htmlspecialchars($user["token"]) ?></code></p>

<h3>Update Info</h3>
<form action="update.php" method="post">
    New Name: <input name="name" value="<?= htmlspecialchars($user["name"]) ?>"><br>
    New Username: <input name="username" value="<?= htmlspecialchars($user["username"]) ?>"><br>
    New Password: <input name="password" type="password"><br>
    <button>Update</button>
</form>

<p><a href="logout.php">Logout</a></p>
</body>
</html>
