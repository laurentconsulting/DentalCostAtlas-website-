<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$message = trim($data["message"] ?? "");
$source = trim($data["source"] ?? "Website");

if (!$name || !$email) {
    http_response_code(400);
    echo json_encode(["error" => "Name and email are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email"]);
    exit;
}

$folder = __DIR__ . "/../data";
$file = $folder . "/leads.csv";

if (!is_dir($folder)) {
    mkdir($folder, 0755, true);
}

$isNewFile = !file_exists($file);

$handle = fopen($file, "a");

if ($isNewFile) {
    fputcsv($handle, ["date", "name", "email", "message", "source"]);
}

fputcsv($handle, [
    date("Y-m-d H:i:s"),
    $name,
    $email,
    $message,
    $source
]);

fclose($handle);

echo json_encode(["success" => true]);
?>
