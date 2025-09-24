<?php
// test_db.php - Run this first to check database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
$host = 'localhost';
$dbname = 'dbtrygpt0dj0pj';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2 style='color:green'>✅ Database connection SUCCESSFUL!</h2>";
 
    // Test tables
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<h3 style='color:green'>✅ Users table exists!</h3>";
    } else {
        echo "<h3 style='color:red'>❌ Users table missing! Run create_tables.sql first.</h3>";
    }
 
} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ Database Error: " . $e->getMessage() . "</h2>";
    echo "<p><strong>Check:</strong></p>";
    echo "<ul>";
    echo "<li>Database 'dbtrygpt0dj0pj' exists?</li>";
    echo "<li>User 'uhpdlnsnj1voi' has permissions?</li>";
    echo "<li>Password 'rowrmxvbu3z5' is correct?</li>";
    echo "<li>MySQL server is running?</li>";
    echo "</ul>";
}
?>
