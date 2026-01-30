<?php
$filePath = __DIR__ . "/emails.txt";

if (!file_exists($filePath)) {
    file_put_contents($filePath, "");
}

function getCount($filePath) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $lines ? count($lines) : 0;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo json_encode([
        "success" => true,
        "count" => getCount($filePath)
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método não permitido."
    ]);
    exit;
}

$email = trim($_POST["email"] ?? "");
if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email inválido."
    ]);
    exit;
}

$emails = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$emails) {
    $emails = [];
}

if (!in_array($email, $emails, true)) {
    $emails[] = $email;
    file_put_contents($filePath, implode(PHP_EOL, $emails) . PHP_EOL);
}

echo json_encode([
    "success" => true,
    "count" => count($emails)
]);
