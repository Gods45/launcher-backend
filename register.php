<?php
header('Content-Type: application/json');

$accountsFile = 'accounts.json';

// Read existing accounts
$accounts = [];
if (file_exists($accountsFile)) {
    $accounts = json_decode(file_get_contents($accountsFile), true);
}

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);
$user = $input['username'] ?? '';
$pass = $input['password'] ?? '';
$expires = $input['expires'] ?? '2099-12-31';

// Validate
if ($user == '' || $pass == '') {
    echo json_encode([
        "success" => false,
        "message" => "Missing username or password"
    ]);
    exit;
}

// Check if user exists
foreach ($accounts as $acc) {
    if ($acc['username'] === $user) {
        echo json_encode([
            "success" => false,
            "message" => "User already exists"
        ]);
        exit;
    }
}

// Add account
$accounts[] = [
    "username" => $user,
    "password" => $pass,
    "expires" => $expires
];

// Save file
file_put_contents($accountsFile, json_encode($accounts, JSON_PRETTY_PRINT));

// Success
echo json_encode([
    "success" => true,
    "message" => "Account created!"
]);