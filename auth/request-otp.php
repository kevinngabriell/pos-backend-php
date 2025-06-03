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
    $input = json_decode(file_get_contents('php://input'), true);
    $otp = random_int(100000, 999999);

    if (
        isset($input['username'])
    ) {
        echo $otp;
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request',
            'response' => [
                'message' => 'Username user are required !!'
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