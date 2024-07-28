<?php
// Enable CORS for local testing
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

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (is_null($email) || is_null($password)) {
    echo json_encode(["message" => "Email and password are required"]);
    exit;
}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        echo json_encode(["message" => "Login successful"]);
    } else {
        echo json_encode(["message" => "Invalid password"]);
    }
} else {
    echo json_encode(["message" => "No user found with this email"]);
}

$stmt->close();
$conn->close();
?>
