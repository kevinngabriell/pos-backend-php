<?php
$host = 'localhost';
$db   = 'POS';
$user = 'kevingabriel';
$pass = ''; // your password, or empty if none
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // echo "Connected successfully using PDO!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
