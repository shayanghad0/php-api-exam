<?php
include "auth.php";
header("Content-Type: application/json");

$rateLimitFile = "rate_limit.json";
$maxRequests = 10;       // max requests per IP
$timeWindow = 60;        // seconds

$ip = $_SERVER['REMOTE_ADDR'];
$now = time();

// Load rate limit data
$rateData = [];
if (file_exists($rateLimitFile)) {
    $rateData = json_decode(file_get_contents($rateLimitFile), true) ?? [];
}

// Clean old entries (older than $timeWindow)
foreach ($rateData as $key => $entry) {
    if ($now - $entry['start_time'] > $timeWindow) {
        unset($rateData[$key]);
    }
}

// Check current IP
if (!isset($rateData[$ip])) {
    $rateData[$ip] = ['count' => 1, 'start_time' => $now];
} else {
    $rateData[$ip]['count']++;
    if ($rateData[$ip]['count'] > $maxRequests) {
        file_put_contents($rateLimitFile, json_encode($rateData, JSON_PRETTY_PRINT));
        http_response_code(429);
        echo json_encode(['error' => 'Too many requests. Please wait before retrying.']);
        exit();
    }
}

// Save updated rate data
file_put_contents($rateLimitFile, json_encode($rateData, JSON_PRETTY_PRINT));

// Token validation
if (!isset($_GET["token"])) {
    echo json_encode(["error" => "Missing token"]);
    exit();
}

$user = findUserByToken($_GET["token"]);
if (!$user) {
    echo json_encode(["error" => "Invalid or expired token"]);
    exit();
}

echo json_encode([
    "success" => true,
    "username" => $user["username"],
    "name" => $user["name"],
    "token_expires_in" => $user["token_expiry"] - (time() - $user["token_time"])
]);
