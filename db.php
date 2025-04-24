<?php
// db.php — подключение к MySQL через PDO
$host   = 'sql112.infinityfree.com';
$db     = 'if0_38824770_time_tracking';
$user   = 'if0_38824770';
$pass   = 'nccKvbNwz0xpX';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
