<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "No account data received"
    ]);
    exit;
}

$username = $data['username'];
$password = $data['password'];

$expirySeconds = isset($data['expiry_seconds']) ? (int)$data['expiry_seconds'] : 0;

$accountsFile = __DIR__ . "/accounts.json";
if (!file_exists($accountsFile)) file_put_contents($accountsFile, json_encode([]));

$accounts = json_decode(file_get_contents($accountsFile), true);

// FIX: your previous version mixed array styles — this keeps it consistent
foreach ($accounts as $acc) {
    if ($acc['username'] === $username) {
        echo json_encode([
            "success" => false,
            "message" => "Account already exists"
        ]);
        exit;
    }
}

$expiry = $expirySeconds > 0 ? date("Y-m-d", time() + $expirySeconds) : "Lifetime";

$accounts[] = [
    'username' => $username,
    'password' => $password,
    'expires' => $expiry
];

file_put_contents($accountsFile, json_encode($accounts, JSON_PRETTY_PRINT));

echo json_encode([
    "success" => true,
    "message" => "Account created successfully",
    "expiry" => $expiry
]);
