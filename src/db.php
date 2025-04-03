<?php
// src/db.php

$host = '127.0.0.1';
$db   = 'db';
$user = 'user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    return new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('DB connection error: ' . $e->getMessage());
}