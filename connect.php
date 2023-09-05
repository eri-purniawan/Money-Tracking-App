<?php

$serverName = 'localhost';
$username = 'root';
$password = '';

try {
  $conn = new PDO("mysql:host=$serverName;dbname=money_tracking", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection Failed: " . $e->getMessage();
}

$table_name = 'keuangan';
