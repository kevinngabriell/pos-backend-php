<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

//Header access is required
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//Display error message
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//Connection access
require_once('../conn/connection.php');

$secret_key = "supersecretkey123";

//Check Method 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['username']) && isset($input['password'])) {
        $username = $input['username'];
        $password = $input['password'];

        try {
            // 1. Ambil user
            $stmt = $pdo->prepare("SELECT id, username, firstname FROM \"User\" WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 2. Ambil password hash
                $stmt2 = $pdo->prepare("SELECT password FROM user_password WHERE userid = :userid");
                $stmt2->execute([':userid' => $user['id']]);
                $passData = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($passData && password_verify($password, $passData['password'])) {
                    // 3. Buat JWT token
                    $issuedAt = time();
                    $expiration = $issuedAt + 3600; // 1 jam

                    $payload = [
                        'iat' => $issuedAt,
                        'exp' => $expiration,
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'firstname' => $user['firstname']
                    ];

                    $jwt = JWT::encode($payload, $secret_key, 'HS256');

                    // 4. Balikin token
                    http_response_code(200);
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Login successful',
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'firstname' => $user['firstname'],
                            'token' => $jwt,
                        ]
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'status' => 401,
                        'message' => 'Invalid password'
                    ]);
                }
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 404,
                    'message' => 'User not found'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Username and password required'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 405,
        'message' => 'Only POST method is allowed'
    ]);
}
?>