<?php
// Header access is required
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Display error message
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Connection access
require_once('../conn/connection.php');

// Timezone UTC WIB
date_default_timezone_set('Asia/Jakarta');

// Check Method 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = json_decode(file_get_contents('php://input'), true);

    if (strpos($contentType, 'application/json') !== false) {

        // Validate input
        if (
            isset($input['first_name']) &&
            isset($input['username']) &&
            isset($input['password']) &&
            isset($input['phone_number']) &&
            isset($input['role'])
        ) {
            $user_id = substr("user" . bin2hex(random_bytes(7)), 0, 15);
            $username = $input['username'];
            $firstname = $input['first_name'];
            $password = password_hash($input['password'], PASSWORD_DEFAULT); // 👈 gunakan hashing!
            $phone_number = $input['phone_number'];
            $role = $input['role'];

            $current_timezone = date('Y-m-d H:i:s');

            try {
                $pdo -> beginTransaction();

                $stmt1 = $pdo->prepare("
                    INSERT INTO \"User\" (id, username, firstname, phone, createdat)
                    VALUES (:id, :username, :firstname, :phone, :currenttimezone)
                ");

                $stmt1->execute([
                    ':id' => $user_id,
                    ':username' => $username,
                    ':firstname' => $firstname,
                    ':phone' => $phone_number,
                    ':currenttimezone' => $current_timezone
                ]);

                $id_password = substr("up" . bin2hex(random_bytes(7)), 0, 15);

                $stmt2 = $pdo->prepare("
                    INSERT INTO user_password (id, userid, password, createdat)
                    VALUES (:upid, :userid, :password, :currenttimezone)
                ");

                $stmt2->execute([
                    ':upid' => $id_password,
                    ':userid' => $user_id,
                    ':password' => $password,
                    ':currenttimezone' => $current_timezone
                ]);

                $id_role = substr("ur" . bin2hex(random_bytes(7)), 0, 15);

                $stmt3 = $pdo->prepare("
                    INSERT INTO user_role (id, userid, roleid)
                    VALUES (:urid, :userid, :role)
                ");

                $stmt3->execute([
                    ':urid' => $id_role,
                    ':userid' => $user_id,
                    ':role' => $role
                ]);

                $pdo->commit();

                http_response_code(201);
                echo json_encode([
                    'status' => 201,
                    'message' => 'Success',
                    'response' => [
                        'message' => "$username account has been successfully created"
                    ]
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 500,
                    'message' => 'Error',
                    'response' => [
                        'message' => $e->getMessage()
                    ]
                ]);
            }

        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => 'Bad Request',
                'response' => [
                    'message' => 'First name, username, password, phone number, and role are required !!'
                ]
            ]);
        }

    } else {
        http_response_code(415);
        echo json_encode([
            'status' => 415,
            'message' => 'Unsupported Media Type',
            'response' => [
                'message' => 'Content-Type must be application/json !!'
            ]
        ]);
    }

} else {
    http_response_code(405);
    echo json_encode([
        'status' => 405,
        'message' => 'Invalid Request Method',
        'response' => [
            'message' => 'Method Not Allowed! Please use POST method.'
        ]
    ]);
}
?>
