<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$user = $input['username'] ?? '';
$keyInput = $input['key'] ?? '';

$accountsFile = __DIR__ . "/accounts.json";
$keysFile = __DIR__ . "/keys.json";

$accounts = json_decode(file_get_contents($accountsFile), true);
$keys = json_decode(file_get_contents($keysFile), true);

$response = ["success" => false, "message" => "Invalid key"];

foreach ($keys as &$key) {
    if ($key['key'] === $keyInput && !$key['used']) {

        foreach ($accounts as &$acc) {
            if ($acc['username'] === $user) {

                $today = strtotime(date("Y-m-d"));
                $currentExpiry = strtotime($acc['expires']);

                $base = ($today > $currentExpiry) ? $today : $currentExpiry;

                $newExpiry = strtotime("+" . $key['days'] . " days", $base);
                $acc['expires'] = date("Y-m-d", $newExpiry);

                $key['used'] = true;

                file_put_contents($accountsFile, json_encode($accounts, JSON_PRETTY_PRINT));
                file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

                $response = ["success" => true, "message" => "Key redeemed!"];
                break 2;
            }
        }
    }
}

echo json_encode($response);
