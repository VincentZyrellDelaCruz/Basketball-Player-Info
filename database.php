<?php
    $host = 'localhost';
    $port = '3306';
    $db   = 'sport';
    $user = 'root';
    $password = '';

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
?>