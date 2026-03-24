<?php
header('Content-Type: application/json');

$accountsFile = __DIR__ . "/accounts.json";

$accounts = [];
if (file_exists($accountsFile)) {
    $accounts = json_decode(file_get_contents($accountsFile), true);
}

$input = json_decode(file_get_contents("php://input"), true);
$user = $input['username'] ?? '';
$pass = $input['password'] ?? '';
$expires = $input['expires'] ?? '2099-12-31';

if ($user == '' || $pass == '') {
    echo json_encode([
        "success" => false,
        "message" => "Missing username or password"
    ]);
    exit;
}

foreach ($accounts as $acc) {
    if ($acc['username'] === $user) {
        echo json_encode([
            "success" => false,
            "message" => "User already exists"
        ]);
        exit;
    }
}

$accounts[] = [
    "username" => $user,
    "password" => $pass,
    "expires" => $expires
];

file_put_contents($accountsFile, json_encode($accounts, JSON_PRETTY_PRINT));

echo json_encode([
    "success" => true,
    "message" => "Account created!"
]);
