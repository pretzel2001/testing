<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

// Debug: Check if the input is received correctly
$input = file_get_contents("php://input");
error_log("Received input: " . $input);

$data = json_decode($input, true);

// Debug: Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    echo json_encode(["message" => "Invalid input"]);
    exit;
}

// Debug: Log the decoded data
error_log("Decoded data: " . print_r($data, true));

if (is_null($data)) {
    echo json_encode(["message" => "Invalid input"]);
    exit;
}

$full_name = $data['fullName'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

// Debug: Log the received fields
error_log("Received fullName: " . $full_name);
error_log("Received email: " . $email);
error_log("Received password: " . $password);

if (is_null($full_name) || is_null($email) || is_null($password)) {
    echo json_encode(["message" => "All fields are required"]);
    exit;
}

// Debug: Log the sanitized data
error_log("Sanitized data - Full Name: $full_name, Email: $email, Password: $password");

$password_hashed = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO users (full_name, email, `password`) VALUES ('$full_name', '$email', '$password_hashed')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Registration successful"]);
} else {
    echo json_encode(["message" => "Error: " . $conn->error]);
}

$conn->close();
?>
