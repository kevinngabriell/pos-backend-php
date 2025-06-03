<?php
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

//Check Method

//Create Role
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //Check the content type
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = json_decode(file_get_contents('php://input'), true);

    if (strpos($contentType, 'application/json') !== false) {

        //Inserting the role name 
        if (isset($input['role']) && !empty($input['role'])) {
            //Collect the role name
            $role = trim($input['role']);
            $role_id = substr("role" . bin2hex(random_bytes(7)), 0, 15);

            try {
                $stmt = $pdo->prepare("INSERT INTO role (id, name, is_active) VALUES (:id, :name, true)");
                $stmt->execute([
                    ':id' => $role_id,
                    ':name' => $role
                ]);
        
                http_response_code(201);
                echo json_encode([
                    'status' => 201,
                    'message' => 'Success',
                    'response' => [
                        'message' => "$role has been successfully created"
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
            // Content-Type is not JSON
            http_response_code(400); // Unsupported Media Type
            echo json_encode([
                'status' => 400,
                'message' => 'Bad Request',
                'response' => [
                    'message' => 'Role name is required !!'
                ]
            ]);
        }

    } else {
        //Sending error for wrong content type
        // Content-Type is not JSON
        http_response_code(415); // Unsupported Media Type
        echo json_encode([
            'status' => 415,
            'message' => 'Bad Request',
            'response' => [
                'message' => 'Content-Type must be application/json !!'
            ]
        ]);
    }
}

//Get Role
else if ($_SERVER['REQUEST_METHOD'] === 'GET'){

    if(isset($_GET['role_id']) or isset($_GET['role'])){

        try {
            if (isset($_GET['role_id'])) {
                $stmt = $pdo->prepare("SELECT * FROM role WHERE id = :id");
                $stmt->execute(['id' => $_GET['role_id']]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM role WHERE name = :role");
                $stmt->execute(['role' => $_GET['role']]);
            }

            $role = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($role) {
                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Success',
                    'response' => [
                        'data' => $role
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 404,
                    'message' => 'Not Found',
                    'response' => [
                        'data' => null
                    ]
                ]);
            }

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

        try {
            $stmt = $pdo->prepare("SELECT * FROM role");
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Success',
                'response' => [
                    'data' => $roles
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


    }

}

else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['role_id'])) {
        $role_id = $_GET['role_id'];

        try {
            // Cek apakah data ada terlebih dahulu
            $check = $pdo->prepare("SELECT * FROM role WHERE id = :id");
            $check->execute(['id' => $role_id]);
            $role = $check->fetch(PDO::FETCH_ASSOC);

            if ($role) {
                $stmt = $pdo->prepare("DELETE FROM role WHERE id = :id");
                $stmt->execute(['id' => $role_id]);

                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Success',
                    'response' => [
                        'message' => "Role with ID $role_id has been deleted"
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 404,
                    'message' => 'Not Found',
                    'response' => [
                        'message' => "Role with ID $role_id not found"
                    ]
                ]);
            }

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
                'message' => 'role_id is required to delete the role!'
            ]
        ]);
    }
}

//Wrong Method
else {
    // Method not allowed for other request types
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 405,
        'message' => 'Invalid Request Method',
        'response' => [
            'message' => 'Method Not Allowed !!Please use POST method are only allowed !!'
        ]
    ]);
}

?>