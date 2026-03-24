<?php
header('Content-Type: application/json');

// --------------------
// READ JSON INPUT
// --------------------
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

// Optional: expiry in seconds from now (0 = unlimited)
$expirySeconds = isset($data['expiry_seconds']) ? (int)$data['expiry_seconds'] : 0;

// --------------------
// LOAD ACCOUNTS
// --------------------
$accountsFile = __DIR__ . '/accounts.json';
if (!file_exists($accountsFile)) file_put_contents($accountsFile, json_encode([]));

$accounts = json_decode(file_get_contents($accountsFile), true);

// --------------------
// CHECK IF USER EXISTS
// --------------------
if (isset($accounts[$username])) {
    echo json_encode([
        "success" => false,
        "message" => "Account already exists"
    ]);
    exit;
}

// --------------------
// CREATE ACCOUNT
// --------------------
$expiry = $expirySeconds > 0 ? time() + $expirySeconds : 0;

$accounts[$username] = [
    'password' => $password,
    'expiry' => $expiry
];

file_put_contents($accountsFile, json_encode($accounts, JSON_PRETTY_PRINT));

echo json_encode([
    "success" => true,
    "message" => "Account created successfully",
    "expiry_timestamp" => $expiry
]);