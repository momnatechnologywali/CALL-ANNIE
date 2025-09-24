<?php
// db.php - Fixed with proper error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
$host = 'localhost';
$dbname = 'dbtrygpt0dj0pj';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // Don't expose real error in production, but helpful for debugging
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check server logs.");
}
 
// Password functions
if (!function_exists('hashPassword')) {
    function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
 
if (!function_exists('verifyPassword')) {
    function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
