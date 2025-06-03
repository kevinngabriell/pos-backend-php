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
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Check if permission_id and username is provided
        if (isset($input['username']) && isset($input['otp']) && isset($input['new_password'])) {
            $username = $input['username'];
            $otp = $input['otp'];
            $newpassowrd = $input['new_password'];

            // Content-Type is not JSON
            http_response_code(200); // Unsupported Media Type
            echo json_encode([
                'status' => 200,
                'message' => 'Success',
                'response' => [
                    'message' => 'Success'
                ]
            ]);
        } else {
            // Content-Type is not JSON
            http_response_code(415); // Unsupported Media Type
            echo json_encode([
                'status' => 400,
                'message' => 'Bad Request',
                'response' => [
                    'message' => 'Username, OTP, and new password is required !!'
                ]
            ]);
        }

    } else {
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

} else {
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