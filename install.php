<?php
// install.php
require 'config.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS staff (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        wallet_balance DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS tournaments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        game_name VARCHAR(50) NOT NULL,
        entry_fee DECIMAL(10,2) NOT NULL,
        prize_pool DECIMAL(10,2) NOT NULL,
        match_time DATETIME NOT NULL,
        total_slots INT NOT NULL,
        status ENUM('Upcoming', 'Ongoing', 'Completed') DEFAULT 'Upcoming',
        room_id VARCHAR(50),
        room_password VARCHAR(50),
        winner_id INT DEFAULT NULL
    )",
    "CREATE TABLE IF NOT EXISTS participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        tournament_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        utr_number VARCHAR(100) NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100),
        image_path VARCHAR(255) NOT NULL
    )"
];

foreach ($tables as $sql) {
    $pdo->exec($sql);
}

// Insert Default Accounts
$defaultPass = password_hash('112233', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT IGNORE INTO admin (username, password) VALUES ('aiwithasjad', ?)");
$stmt->execute([$defaultPass]);

$stmt = $pdo->prepare("INSERT IGNORE INTO staff (username, password) VALUES ('staff', ?)");
$stmt->execute([$defaultPass]);

echo "<h3>Installation Complete! Database, Tables, and Default Accounts Created.</h3>";
echo "<a href='login.php'>Go to Login</a>";
?>
