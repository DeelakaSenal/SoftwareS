<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Corrected the path

use Dotenv\Dotenv;

// Corrected the syntax for creating the Dotenv instance
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function getDBConnection() {
    $host = $_ENV['DB_HOST'];
    $db = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $port = $_ENV['DB_PORT'];
    $sslCa = $_ENV['DB_SSL_CA'];
    $charset = 'utf8mb4';

    // Corrected the DSN format
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_SSL_CA       => $sslCa,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Optional, depending on your certificate
    ];

    try {
        // Create the PDO instance
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Handle any errors by throwing an exception with the message and code
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>
