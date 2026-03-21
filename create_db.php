<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', 'fang12345');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS CVactive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "Database 'CVactive' created successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
