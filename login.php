<?php
header("Content-Type: application/json");

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No login data received"]);
    exit;
}

$username = $data["username"] ?? "";
$password = $data["password"] ?? "";

// Load accounts
$accounts = json_decode(file_get_contents("accounts.json"), true);

foreach ($accounts as $acc) {
    if ($acc["username"] === $username && $acc["password"] === $password) {

        // 🔥 Lifetime account
        if ($acc["expires"] === 0 || strtolower($acc["expires"]) === "lifetime") {
            echo json_encode([
                "success" => true,
                "message" => "Login successful!",
                "expiry" => "Lifetime"
            ]);
            exit;
        }

        // Convert expiry to timestamp
        $expiryTime = strtotime($acc["expires"]);
        $currentTime = time();

        // ❌ Expired
        if ($expiryTime < $currentTime) {
            echo json_encode([
                "success" => false,
                "message" => "Account expired!"
            ]);
            exit;
        }

        // 🔥 Calculate remaining time
        $remaining = $expiryTime - $currentTime;

        $days = floor($remaining / 86400);
        $hours = floor(($remaining % 86400) / 3600);
        $minutes = floor(($remaining % 3600) / 60);

        // Build string like "2D 3H 37M"
        $timeString = "";
        if ($days > 0) $timeString .= $days . "D ";
        if ($hours > 0) $timeString .= $hours . "H ";
        if ($minutes > 0) $timeString .= $minutes . "M";

        echo json_encode([
            "success" => true,
            "message" => "Login successful!",
            "expiry" => trim($timeString)
        ]);
        exit;
    }
}

// ❌ Invalid login
echo json_encode([
    "success" => false,
    "message" => "Invalid login"
]);